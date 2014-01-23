--  Generate SQL 
--  Version:                   	V5R4M0 060210 
--  Generated on:              	03/08/12 11:51:39 
--  Relational Database:       	S10F6F71 
--  Standards Option:          	DB2 UDB iSeries 
DROP TABLE APLUS08FIN.PT_AU ; 
  
CREATE TABLE APLUS08FIN.PT_AU ( 
	ID INTEGER GENERATED ALWAYS AS IDENTITY ( 
	START WITH 1 INCREMENT BY 1 
	NO MINVALUE NO MAXVALUE 
	NO CYCLE NO ORDER 
	CACHE 20 ) 
	, 
	"KEY" VARCHAR(255) CCSID 37 DEFAULT NULL , 
	TOKEN VARCHAR(255) CCSID 37 DEFAULT NULL , 
	SECRET VARCHAR(255) CCSID 37 DEFAULT NULL , 
	CONSTRAINT APLUS08FIN.Q_APLUS08FIN_PT_AU_ID_00001 PRIMARY KEY( ID ) )   
	; 
  
LABEL ON TABLE APLUS08FIN.PT_AU 
	IS 'Production Tools Api Users Table' ; 
  
LABEL ON COLUMN APLUS08FIN.PT_AU 
( ID IS 'ID                  ID                  ID' , 
	"KEY" IS 'KEY                 KEY                 KEY' , 
	TOKEN IS 'TOKEN               TOKEN               TOKEN' , 
	SECRET IS 'SECRET              SECRET              SECRET' ) ; 
  
LABEL ON COLUMN APLUS08FIN.PT_AU 
( ID TEXT IS 'ID Column' , 
	"KEY" TEXT IS 'KEY' , 
	TOKEN TEXT IS 'TOKEN' , 
	SECRET TEXT IS 'SECRET' ) ;
