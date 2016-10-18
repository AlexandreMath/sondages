BEGIN TRANSACTION;
CREATE TABLE responses(id integer primary key autoincrement, id_survey integer, title char(255), count integer);
INSERT INTO responses VALUES(1,1,'Bleu',4);
INSERT INTO responses VALUES(2,1,'Noir',0);
INSERT INTO responses VALUES(3,1,'Jaune',0);
INSERT INTO responses VALUES(4,2,'Lundi',0);
INSERT INTO responses VALUES(5,2,'Samedi',6);
INSERT INTO responses VALUES(6,2,'Dimanche',10);
INSERT INTO responses VALUES(7,3,'10 ans',25);
INSERT INTO responses VALUES(8,3,'25 ans',0);
INSERT INTO responses VALUES(9,3,'100 ans',2);
INSERT INTO responses VALUES(10,4,'Sucré',0);
INSERT INTO responses VALUES(11,4,'Salé',0);
INSERT INTO responses VALUES(12,5,1515,0);
INSERT INTO responses VALUES(13,5,1789,12);
INSERT INTO responses VALUES(14,5,1999,0);
CREATE TABLE surveys(id integer primary key autoincrement, owner char(20), question char(255));
INSERT INTO surveys VALUES(1,'marc','Quelle est votre couleur préférée?');
INSERT INTO surveys VALUES(2,'marc','Quel est votre jour de la semaine préféré?');
INSERT INTO surveys VALUES(3,'paul','Combien d''années représentent une décennie?');
INSERT INTO surveys VALUES(4,'marc','Vous préférez sucré ou salé?');
INSERT INTO surveys VALUES(5,'paul','Quel est l''année de la Révolution française?');
CREATE TABLE users(nickname char(20), password char(50));
INSERT INTO users VALUES('marc','250cf8b51c773f3f8dc8b4be867a9a02');
INSERT INTO users VALUES('paul','202cb962ac59075b964b07152d234b70');
INSERT INTO users VALUES('andré','68053af2923e00204c3ca7c6a3150cf7');
INSERT INTO users VALUES('bruno','202cb962ac59075b964b07152d234b70');
INSERT INTO users VALUES('fred','202cb962ac59075b964b07152d234b70');
COMMIT;