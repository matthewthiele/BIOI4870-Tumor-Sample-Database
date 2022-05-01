
# HuTTS

The Human Tumor Tissue Sample database, or HuTTS, is an 
implementation of a tumor sample-specific database that takes 
NCBI Biosample and SRA (Sequence Read Archive) IDs and provides 
further contextual information, currently in the form of 
supplemental data from the NCG (Network of Cancer Genes) database.
It does so in an effort to establish a useful format for 
exploring human tumor tissue samples, as there are currently no 
mainstream databases that focus on this particular facet of 
research. Rather, more general databasees such as Biosample fill 
this need. HuTTS in particular is geared towards data analysis in
cancer research, connecting samples with information relevant to 
studying them with bioinformatic processes.

HuTTS provides three functions in its current form. 
First, the ability to browse through cancer types that are within 
the database. Due to the difficulty of information retrieval, 
HuTTS provides a way for users to find specific cancer types 
through exploring them under organ systems and primary sites. 
Second, the ability to retrieve lists of canonical cancer drivers 
documented by NCG by either specific cancer type or primary site 
of the tumor, which are linked to various forms of cancer 
through peer-reviewed literature. Third, the ability to search 
for samples by cancer type or primary site of the tumor. Users 
will be presented with all samples within the database that match 
the search term. 

The intended workflow is as follows: A user consults the page for
cancer types, settling on either a specific type or the primary 
site of the tumor. The user then searches the database for 
samples that match the desired cancer type, and are presented with
Biosample and SRA links along with relevant information such as 
age, sex, and ethnicity, among other details. Finally, the user 
uses the gene search to find genes of interest that correspond 
with their cancer type, giving a path for future research using 
the sample sequence information.

HuTTS was designed and implemented as my personal project in the 
University of Nebraska at Omaha's BIOI4870 course, Database 
Search and Pattern Discovery in Bioinformatics. As such, it is 
limited in scope by both data storage and development time. 
There are many more features that HuTTS could present on top of 
its current functionality, but it represents a path towards 
establishing other similar databases for furthering research 
into cancer genetics.
## Documentation

To setup HuTTS on your own machine, follow the steps below. 
The necessary file for generating NCG-related tables can be 
downloaded from http://ncg.kcl.ac.uk/download.php, as 
"List of all 3347 cancer drivers and their annotation and 
supporting evidence:" under "Annotation and supporting evidence." 

1. Requirements
 
HuTTS was built using Python 3.9.7, PHP, HTML, and MariaDB.
HuTTS was built using these libraries, although more current 
versions should still function:

Biopython (1.79) 

numpy (1.21.3)

pandas (1.3.4)

unidecode

2. Setup 

To set up HuTTS on your machine after downloading the repository 
to the desired directory, ensure that you have downloaded the NCG 
.tsv file linked above, and placed it in the same directory. 

Review cancer_dict.tsv and sample_files.txt. These are currently 
filled with the necessary info to implement 100 existing samples 
into the HuTTS database. If you wish to add other samples into 
the database, find the corresponding Biosample and SRA entries, 
and append their respective ids to sample_files.txt. Biosample ids
begin with 'SAMN' while SRA ids begin with 'SRX'. Following this 
convention allows Biopython to access the entries through NCBI's 
Entrez API.

If you add samples, make sure that you also update cancer_dict.tsv
with the necessary information. When creating the DML code, 
dml_generator.py reads this file into a dictionary, and converts 
disease attributes on the sample XML data to valid cancer types, 
which can be input into the database. To add to cancer_dict.tsv, 
add a line that begins with the 'disease' attribute from your 
sample, tab, then add the corresponding cancer type. Consult the 
generated cancer_categories table if you need to find the 
appropriate cancer_type for your samples. Some entries may already
match their respective cancer type.

You are now ready to generate the DML. Open dml_generator.py, and 
replace my email with your own for connecting to Entrez. Run the 
program, and you should end with an output file 
'BIOI4870-Tumor-Sample-Database-DML.sql' which contains all DML 
necessary to generate the database.

In your terminal, enter 'mysql [database] < 
BIOI4870-Tumor-Sample-Database-DDL.sql' followed by 'mysql 
[database] < BIOI4870-Tumor-Sample-Database-DML.sql' to generate 
the database. If you encounter errors, you may need to update the 
DDL to allow for longer entries in some fields.

HuTTS should be functioning at this point! On cancer.php, 
genes.php, and sample.php, change the database credentials at the
top to match your own credentials. You may now launch the website 
portion of HuTTS.
