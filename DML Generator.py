import glob
import xml.etree.ElementTree as ET
import pandas as pd
import numpy as np
from io import StringIO
import unidecode

# Create output file
output_file = 'BIOI4870-Tumor-Sample-Database-DML.sql'
with open(output_file, 'w+') as f:
   pass

# Create DML for cancer_categories table, this is done first for the biosample table
# to compare 'disease' strings to 'cancer_type' strings present in cancer_categories
input_file = 'NCG_cancerdrivers_annotation_supporting_evidence.tsv'
cancer = pd.read_csv(input_file, sep="\t")

cancer = cancer.drop(columns = ['entrez','symbol','pubmed_id','type','method','coding_status',
   'cgc_annotation','vogelstein_annotation','saito_annotation','NCG_oncogene','NCG_tsg'])
cancer = cancer.dropna(how='any')
cancer = cancer.drop_duplicates()
cancer = cancer.sort_values(by=['primary_site'])
cancer = cancer.sort_values(by=['organ_system'])
print(cancer)

with open(output_file, 'a') as f:
   print("--DML for cancer_categories", file=f)
   for i in cancer.itertuples(index=False, name=None):
      print("INSERT INTO cancer_categories VALUES {};".format(str(i).replace('_',' ')), file=f)

# Create dict object from cancer_dict.tsv
cancer_dict = {}
with open('cancer_dict.tsv', 'r') as f:
   for i in f:
      key, value = i.split('\t')
      cancer_dict[key] = value.strip('\n')

# Pull biosample and SRA data from samples folder
samples = glob.glob('samples/*.xml')

sra_attributes = StringIO("sra_id,link,study,instrument,strategy,source,selection,layout")
biosample_attributes = StringIO("biosample_id,sra_id,biomaterial_provider,sample_type,isolate,"
   +
   "tissue,cell_type,disease_stage,cancer_type,phenotype,ethnicity,population,age,sex")

sra = pd.read_csv(sra_attributes)
biosample = pd.read_csv(biosample_attributes)

for i in samples:
   tree = ET.parse(i)
   root = tree.getroot()
   # Parse Biosample data
   if root.tag == 'BioSampleSet':
      # Create dict for values to be inserted into dataframe
      biosample_add = dict(
         biosample_id = '',
         sra_id = '',
         biomaterial_provider = '',
         sample_type = '',
         isolate = '',
         tissue = '',
         cell_type = '',
         disease_stage = '',
         cancer_type = '',
         phenotype = '',
         ethnicity = '',
         population = '',
         age = '',
         sex = ''
      )
      for id in root.find('BioSample').find('Ids'):
         if 'db' in id.attrib:
            # Set biosample_id value
            if id.get('db') == 'BioSample':
               biosample_add['biosample_id'] = id.text
            # Set sra_id value
            elif id.get('db') == 'SRA':
               biosample_add['sra_id'] = id.text
      # For each attribute, check for identical key in biosample_add dict, and if present, set key value to text of xml attribute
      for attribute in root.find('BioSample').find('Attributes'):
         if attribute.get('harmonized_name') in biosample_add:
            if attribute.get('harmonized_name') == 'biomaterial_provider':
               # Removes accents from biomaterial_provider string they do not agree with SQL
               biosample_add[attribute.get('harmonized_name')] = unidecode.unidecode(attribute.text)
            else:
               biosample_add[attribute.get('harmonized_name')] = attribute.text
         elif attribute.get('harmonized_name') == 'disease':
            # Converts disease string to one recognized by cancer_categories and cancer_genes table
            if attribute.text in cancer_dict:
               biosample_add['cancer_type'] = cancer_dict[attribute.text]
            else:
               for disease in cancer['cancer_type']:
                  if disease.lower().replace('_',' ') == attribute.text.lower().replace('_',' '):
                     biosample_add['cancer_type'] = disease.lower().replace('_',' ')
      pd_add = pd.DataFrame.from_dict([biosample_add])
      # Add biosample_add dict to dataframe
      biosample = pd.concat([biosample, pd_add], ignore_index=True)
      
   # Parse SRA data
   elif root.tag == 'EXPERIMENT_PACKAGE_SET':
      # Create dict for values to be inserted into dataframe
      sra_add = dict(
         sra_id = '',
         link = '',
         study = '',
         instrument = '',
         strategy = '',
         source = '',
         selection = '',
         layout = ''
      )
      # Set sra_id value
      sra_add['sra_id'] = root.find('EXPERIMENT_PACKAGE').find('EXPERIMENT').find('DESIGN').find('SAMPLE_DESCRIPTOR').get('accession')
      # Set link value
      sra_add['link'] = "https://www.ncbi.nlm.nih.gov/sra/{}".format(sra_add['sra_id'])
      # Set study value
      sra_add['study'] = root.find('EXPERIMENT_PACKAGE').find('EXPERIMENT').find('TITLE').text
      # Set instrument value
      for instrument in root.find('EXPERIMENT_PACKAGE').find('EXPERIMENT').iter('INSTRUMENT_MODEL'):
         sra_add['instrument'] = instrument.text
      for library_descriptor in root.find('EXPERIMENT_PACKAGE').find('EXPERIMENT').find('DESIGN').find('LIBRARY_DESCRIPTOR'):
         # Set strategy value
         if library_descriptor.tag == 'LIBRARY_STRATEGY':
            sra_add['strategy'] = library_descriptor.text
         # Set source value
         elif library_descriptor.tag == 'LIBRARY_SOURCE':
            sra_add['source'] = library_descriptor.text
         # Set selection value
         elif library_descriptor.tag == 'LIBRARY_SELECTION':
            sra_add['selection'] = library_descriptor.text
         # Set layout value
         elif library_descriptor.tag == 'LIBRARY_LAYOUT':
            for child in library_descriptor:
               sra_add['layout'] = child.tag
      pd_add = pd.DataFrame.from_dict([sra_add])
      # Add sra_add dict to dataframe
      sra = pd.concat([sra, pd_add], ignore_index=True)

print(biosample)
print(sra)
with open(output_file, 'a') as f:
   # Create DML for biosample table from pandas dataframe
   print("--DML for biosample", file=f)
   for i in biosample.itertuples(index=False, name=None):
      print("INSERT INTO biosample VALUES {};".format(i), file=f)
   # Create DML for sra table from pandas dataframe
   print("--DML for sra", file=f)
   for i in sra.itertuples(index=False, name=None):
      print("INSERT INTO sra VALUES {};".format(i), file=f)
      
# Create DML for cancer_genes table
genes = pd.read_csv(input_file, sep="\t")

genes = genes.drop(columns = ['entrez','organ_system', 'primary_site','pubmed_id','type','method','coding_status',
   'cgc_annotation','vogelstein_annotation','saito_annotation'])
genes = genes.dropna(how='any')
genes = genes.drop(columns = ['NCG_oncogene','NCG_tsg'])
genes = genes.drop_duplicates()
genes = genes.reindex(columns = ['cancer_type', 'symbol'])
print(genes)

with open(output_file, 'a') as f:
   print("--DML for cancer_genes", file=f)
   for i in genes.itertuples(index=False, name=None):
      print("INSERT INTO cancer_genes VALUES {};".format(str(i).replace('_',' ')), file=f)