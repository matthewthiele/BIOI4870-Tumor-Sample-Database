--DDL for biosample
CREATE TABLE biosample(
	biosample_id VARCHAR(15) NOT NULL,
	sra_id VARCHAR(15) NOT NULL,
	biomaterial_provider TEXT,
	sample_type VARCHAR(60),
	isolate VARCHAR(60),
	tissue VARCHAR(60),
	cell_type VARCHAR(60),
	disease_stage VARCHAR(60),
	cancer_type VARCHAR(60),
	phenotype VARCHAR(60),
	ethinicity VARCHAR(30),
	population VARCHAR(30),
	age VARCHAR(15),
	sex VARCHAR(15)
);

--DDL for sra
CREATE TABLE sra(
	sra_id VARCHAR(15) NOT NULL,
	link VARCHAR(60) NOT NULL,
	study TEXT,
	instrument VARCHAR(30),
	strategy VARCHAR(30),
	source VARCHAR(30),
	selection VARCHAR(30),
	layout VARCHAR(15)
);

--DDL for cancer_categories
CREATE TABLE cancer_categories(
	organ_system VARCHAR(30) NOT NULL,
	primary_site VARCHAR(30) NOT NULL,
	cancer_type VARCHAR(60) NOT NULL
);

--DDL for cancer_genes
CREATE TABLE cancer_genes (
	cancer_type VARCHAR(60) NOT NULL,
	symbol VARCHAR(12) NOT NULL
);
