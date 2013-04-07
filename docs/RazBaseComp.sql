

-- TP1 : Création des tables



DROP TABLE IF EXISTS visiter;
DROP TABLE IF EXISTS ressembler;
DROP TABLE IF EXISTS bien;
DROP TABLE IF EXISTS demande;
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS typebien;

CREATE TABLE client (
	idclient	int AUTO_INCREMENT,
	nomclient	varchar(30),
	adrclient	varchar(50),
	telclient	varchar(10),
	emailclient	varchar(20),
	CONSTRAINT pk_client PRIMARY KEY(idclient)
) ENGINE=InnoDB CHARSET=UTF8;

CREATE TABLE typebien (
	idtype	char(2),
	nomtype	varchar(30),
	CONSTRAINT pk_typebien PRIMARY KEY(idtype)
) ENGINE=InnoDB CHARSET=UTF8;

CREATE TABLE demande (
	iddemande	int AUTO_INCREMENT,
	datedemande	datetime,
	disponibilite	varchar(50),
	idclient	int,
	CONSTRAINT pk_demande PRIMARY KEY(iddemande),
	CONSTRAINT fk_demande_client FOREIGN KEY(idclient) REFERENCES client(idclient)
) ENGINE=InnoDB CHARSET=UTF8;

CREATE TABLE bien (
	idbien	char(5),
	titrebien	varchar(30),
	detailbien	varchar(100),
	adrbien	varchar(50),
	prixbien	decimal(8,2),
	idtype	char(2),
	CONSTRAINT pk_bien PRIMARY KEY(idbien),
	CONSTRAINT fk_bien_typebien FOREIGN KEY(idtype) REFERENCES typebien(idtype)
) ENGINE=InnoDB CHARSET=UTF8;

CREATE TABLE enchere (
  idbien char(5),
  debut datetime,
  fin datetime,
  prixdepart  decimal(10,2),
  CONSTRAINT pk_enchere PRIMARY KEY(idbien),
  CONSTRAINT fk_enchere_bien FOREIGN KEY(idbien) REFERENCES bien(idbien)
) ENGINE=InnoDB CHARSET=UTF8;

CREATE TABLE surencherir (
  idbien char(5),
  idclient  int,
  dateenchere datetime,
  montant decimal(10,2),
  CONSTRAINT pk_surencherir PRIMARY KEY(idbien,idclient,dateenchere),
  CONSTRAINT fk_surencherir_bien FOREIGN KEY(idbien) REFERENCES enchere(idbien),
  CONSTRAINT fk_surencherir_client FOREIGN KEY(idclient) REFERENCES client(idclient)
) ENGINE=InnoDB CHARSET=UTF8;

CREATE TABLE ressembler (
	idbien1	char(5),
	idbien2	char(5),
	ordre	int,
	CONSTRAINT pk_ressembler PRIMARY KEY(idbien1,idbien2),
	CONSTRAINT fk_ressembler_bien1 FOREIGN KEY(idbien1) REFERENCES bien(idbien),
	CONSTRAINT fk_ressembler_bien2 FOREIGN KEY(idbien2) REFERENCES bien(idbien)
) ENGINE=InnoDB CHARSET=UTF8;

CREATE TABLE visiter (
	idbien	char(5),
	iddemande int,
	priorite	int,
	CONSTRAINT pk_visiter PRIMARY KEY(idbien,iddemande),
	CONSTRAINT fk_visiter_bien FOREIGN KEY(idbien) REFERENCES bien(idbien),
	CONSTRAINT fk_visiter_demande FOREIGN KEY(iddemande) REFERENCES demande(iddemande)
) ENGINE=InnoDB CHARSET=UTF8;


-- Insertion dans la base

delete from visiter;
delete from ressembler;
delete from bien;
delete from demande;
delete from client;
delete from typebien;

insert into typebien(idtype,nomtype) values('F1','Une pièce');
insert into typebien(idtype,nomtype) values('F2','Deux pièces');
insert into typebien(idtype,nomtype) values('F3','Trois pièces');
insert into typebien(idtype,nomtype) values('F4','Quatre pièces');
insert into typebien(idtype,nomtype) values('F5','Cinq pièces');
insert into typebien(idtype,nomtype) values('F6','Six pièces');
insert into typebien(idtype,nomtype) values('F7','Sept pièces');
insert into typebien(idtype,nomtype) values('FG','Plus de 7 pièces');


insert into bien(idbien,titrebien,detailbien,adrbien,prixbien,idtype)
values('b0001','Villa Rieumes','Centre de Rieumes, jolie maison de plain-pied dans un charmant quartier ','Place du village, 31900 Rieumes',270000,'F7');

insert into bien(idbien,titrebien,detailbien,adrbien,prixbien,idtype)
values('b0002','Villa Garidech','Dans quartier résidentiel, jolie villa F5 de plain-pied,  cuisine équipée ','Place du centre, 31800 Garidech',320000,'F5');

insert into bien(idbien,titrebien,detailbien,adrbien,prixbien,idtype)
values('b0003','Villa Plaisance','Belle maison de caractère F5 150 m2 sur terrain 500 m2 calme. Très beau séjour en L 43 m2 cheminée','Rue droite, 31700 Plaisance',250000,'F5');

insert into bien(idbien,titrebien,detailbien,adrbien,prixbien,idtype)
values('b0004','Villa Beaumont','Ferme habitable en l''état composée de 3 chambres, dont une au RDC, salon, cuisine, combles ','Rue gauche, 82500 Beaumont',175000,'F4');

insert into bien(idbien,titrebien,detailbien,adrbien,prixbien,idtype)
values('b0005','Villa Auterive','Dans cadre champêtre, villa neuve F4 sur 700 m² de terrain clos, disponible  ','Avenue du centre, 31500 Auterive',215000,'F4');

insert into bien(idbien,titrebien,detailbien,adrbien,prixbien,idtype)
values('b0006','Villa St Rustice','Maison ancienne F4 sur 3000M² clos dont 1500M² constructible, dépendances','Avenue extérieure, 31430 St Rustice',245000,'F4');

insert into bien(idbien,titrebien,detailbien,adrbien,prixbien,idtype)
values('b0007','Villa L''Union','Charmante maison de plain pied avec garage, abri de voiture, proche commerces','Rue des granges, 31110 L''Union',195000,'F4');

insert into bien(idbien,titrebien,detailbien,adrbien,prixbien,idtype)
values('b0008','Villa Léguevin','Située dans un environnement très calme, vous serez séduits par les volumes et la luminosité','Rue fauve, 31220 Léguevin',250000,'F5');

insert into bien(idbien,titrebien,detailbien,adrbien,prixbien,idtype)
values('b0009','Villa Bessières','Agréable maison de plain pied (2003) 117m2,4 CH dont une suite parentale. Cuisine U.S,grand séjour','Rue courbe, 31670 Bessières',275000,'F5');

insert into bien(idbien,titrebien,detailbien,adrbien,prixbien,idtype)
values('b0010','Villa St Lys','Proche ttes commodités au calme, agréable villa F4 de 95 m2, cellier, garage indépendant','Chemin grand, 31550 St Lys',245000,'F4');


insert into client(idClient,nomclient,adrclient,telclient,emailclient) values
(1,'Michel Tuffery','Université Toulouse 2','0562747575','tuffery@cict.fr');

insert into demande(idDemande,datedemande,disponibilite,idclient) values
(1,'2012-09-12','Lundi matin et Jeudi après-midi',1);

insert into visiter(idbien,iddemande,priorite) values
('b0001',1,1);
insert into visiter(idbien,iddemande,priorite) values
('b0002',1,2);
insert into visiter(idbien,iddemande,priorite) values
('b0007',1,3);

insert into demande(idDemande,datedemande,disponibilite,idclient) values
(2,'2012-10-20','Mardi matin et Vendredi après-midi',1);

insert into visiter(idbien,iddemande,priorite) values
('b0003',2,1);
insert into visiter(idbien,iddemande,priorite) values
('b0004',2,2);



insert into client(idClient,nomclient,adrclient,telclient,emailclient) values
(2,'Monsieur Intranet','Rue du DotNet','0561508765','intranet@cict.fr');

insert into demande(idDemande,datedemande,disponibilite,idclient) values
(3,'2012-10-21','Lundi après-midi  et Jeudi après-midi',2);

insert into visiter(idbien,iddemande,priorite) values
('b0009',3,1);
insert into visiter(idbien,iddemande,priorite) values
('b0010',3,2);
insert into visiter(idbien,iddemande,priorite) values
('b0008',3,3);
insert into visiter(idbien,iddemande,priorite) values
('b0002',3,4);


insert into ressembler(idbien1,idbien2,ordre) values
('b0002','b0003',1);
insert into ressembler(idbien1,idbien2,ordre) values
('b0002','b0008',2);
insert into ressembler(idbien1,idbien2,ordre) values
('b0002','b0009',3);
insert into ressembler(idbien1,idbien2,ordre) values
('b0004','b0007',1);
insert into ressembler(idbien1,idbien2,ordre) values
('b0004','b0006',2);
insert into ressembler(idbien1,idbien2,ordre) values
('b0006','b0004',1);
insert into ressembler(idbien1,idbien2,ordre) values
('b0006','b0005',2);
insert into ressembler(idbien1,idbien2,ordre) values
('b0009','b0002',1);
insert into ressembler(idbien1,idbien2,ordre) values
('b0009','b0003',2);


-- TP2 : Ajouts dans la base

ALTER TABLE `bien` ADD `photoBien` varchar(20);
UPDATE bien b SET b.photobien = CONCAT(b.idbien,'.jpg');

INSERT INTO client VALUES (3,'Aurélien Bertron','1 rue Terray','0607149286','aurelienbertron@gmail.com');
INSERT INTO demande VALUES (4,SYSDATE(),'Jeudi après-midi et Samedi',3);
INSERT INTO visiter VALUES ('b0001',4,1);
INSERT INTO visiter VALUES ('b0002',4,2);

-- TP9 : ajouts des enchères

INSERT INTO enchere VALUES ('b0001',SYSDATE(),SYSDATE()+10/(24*60),100000);
INSERT INTO enchere VALUES ('b0002',SYSDATE(),SYSDATE()+30/(24*60),150000);
INSERT INTO enchere VALUES ('b0003',SYSDATE(),SYSDATE()+25/(24*60),125000);
INSERT INTO enchere VALUES ('b0004',SYSDATE(),SYSDATE()+40/(24*60),100000);
INSERT INTO enchere VALUES ('b0005',SYSDATE(),SYSDATE()+1/24,160000);