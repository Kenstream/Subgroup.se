INSERT INTO `elements` (`type`, `sequence`, `label`, `infoText`, `infoType`, `jsonDefaultValue`, `scenarioId`, `sectionId`, `isRequired`, `assessmentCategoryId`) VALUES
('text', 2, 'Gruppens formella, uttalade mål och mening', NULL, NULL, NULL, 1, 1, 1, NULL),
('text', 3, 'Antal år inom denna organisation', NULL, NULL, NULL, 1, 1, 1, NULL),
('checkbox', 4, 'Högsta ledning', NULL, NULL, NULL, 1, 21, 1, NULL),
('checkbox', 5, 'Mellanchefsnivå', NULL, NULL, NULL, 1, 21, 1, NULL),
('checkbox', 6, 'Arbete med eller utan särskild yrkesutbildning', NULL, NULL, NULL, 1, 21, 1, NULL),
('checkbox', 7, 'Arbete som kräver kortare högskoleutbildning', NULL, NULL, NULL, 1, 21, 1, NULL),
('checkbox', 8, 'Specialistkompetens', NULL, NULL, NULL, 1, 21, 1, NULL),
('checkbox', 9, 'Annan befattning', NULL, NULL, NULL, 1, 21, 1, NULL),

('radio', 10, 'Gruppens medlemmar har varit samma sedan gruppens start', NULL, NULL, '{"values":{"1":"Nej","2":"Ja"}}', 1, 2, 1, NULL),
('textarea', 11, 'Om inte, vilka förändringar har skett? (En vanlig fundering är om gruppen blir ny, om en medlem försvinner/tillkommer. Så länge majoriteten av medlemmarna i gruppen finns kvar och gruppens huvudsakliga mål/fokus är detsamma är det samma grupp).
Beskriv ditt svar', NULL, NULL, NULL, 1, 2, 0, NULL),
('radio', 12, 'Det är tydligt vilka som är med i gruppen', NULL, NULL, '{"values":{"1":"Nej","2":"Ja"}}', 1, 2, 1, NULL),
('radio', 13, 'Jag vet vilka som är med eller inte med i gruppens olika sammankomster', NULL, NULL, '{"values":{"1":"Nej","2":"Ja"}}', 1, 2, 1, NULL),
('radio', 14, 'Vet du hur länge gruppen kommer att arbeta ihop?', NULL, NULL, '{"values":{"1":"Nej","2":"Ja"}}', 1, 2, 1, NULL),
('radio', 15, 'För att nå uppsatta mål krävs mycket kommunikation och samarbete inom gruppen', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 3, 1, NULL),
('radio', 16, 'Vi träffas tillräckligt ofta för att utföra gruppens arbetsuppgifter (t ex informera varandra, koordinera viktiga aktiviteter, rådgöra med varandra, fatta beslut)', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 3, 1, NULL),

('radio', 17, 'Gruppens medlemmar har rätt kunskaper för att kunna uppnå gruppmålen?', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 4, 1, NULL),
('radio', 18, 'Gruppens medlemmar har de sociala färdigheter som krävs för att samarbeta', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 4, 1, NULL),
('radio', 19, 'Varje gruppmedlem tillför unika bidrag till gruppen utifrån kompetens, färdigheter och erfarenheter', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 4, 1, NULL),
('radio', 20, 'Alla medlemmar vet vilka beteendenormer som gäller', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 4, 1, NULL),
('radio', 21, 'Alla medlemmarna är överens om vilka beteendenormer som gäller', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 4, 1, NULL),
('radio', 22, 'Vi är lämpligt antal medlemmar i gruppen utifrån gruppens arbetsuppgift', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 4, 1, NULL),
('radio', 23, 'Arbetslagets arbetsuppgift är tydlig', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 4, 1, NULL),
('radio', 24, 'Arbetslagets arbetsuppgifter är meningsfulla och engagerande', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 4, 1, NULL),
('radio', 25, 'De arbetsuppgifter vi utför i gruppen förutsätter att medlemmarna gör egna bedömningar på basis av egen erfarenhet och kompetens', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 4, 1, NULL),
('radio', 26, 'Arbetslaget får tydlig återkoppling kring hur väl laget presterar', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 4, 1, NULL),

('radio', 27, 'Ledaren fastställer målen', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 5, 1, NULL),
('radio', 28, 'Arbetetslagets mål och mening är tydligt för alla medlemmar', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 5, 1, NULL),
('radio', 29, 'Målen är utmanande', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 5, 1, NULL),
('radio', 30, 'Medlemmarna måste anstränga sig för att nå målen', NULL, NULL, '{"values":{"5":"1","4":"2","3":"3","2":"4","1":"5"}}', 1, 5, 1, NULL),
('radio', 31, 'Resultaten av gruppens arbete har stor betydelse för den övriga organisationen', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 5, 1, NULL),

('radio', 32, 'Gruppen har rätt materiella förutsättningar för att kunna utföra sitt arbete', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 6, 1, NULL),
('radio', 33, 'Medlemmarna i gruppen har rätt kompetens', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 6, 1, NULL),
('radio', 34, 'Gruppen har möjlighet till kompetensutveckling', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 6, 1, NULL),
('radio', 35, 'Gruppen har tillgång till den information som krävs för att utföra sitt arbete', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 6, 1, NULL),
('radio', 36, 'Gruppen har lämpliga ekonomiska incitament för att utföra sitt arbete', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 6, 1, NULL),
('radio', 37, 'Bra prestation hos arbetslag uppmärksammas positivt i vår organisation', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 6, 1, NULL),

('radio', 38, 'Coachar enskilda individer', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5", "0": "Vet ej"}}', 1, 7, 1, NULL),
('radio', 39, 'Hjälper gruppmedlemmar att samarbeta', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5", "0": "Vet ej"}}', 1, 7, 1, NULL),
('radio', 40, 'Arbetar med utformning av teamet: t ex fastställa mål, välja medlemmar, strukturera uppgifter', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5", "0": "Vet ej"}}', 1, 7, 1, NULL),
('radio', 41, 'Förbättra de yttre förutsättningarna för gruppen- t ex frigör resurser, söker yttre stöd m.m.', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5", "0": "Vet ej"}}', 1, 7, 1, NULL),

('radio', 42, 'Jag hjälper gruppen att förbättra sina arbetsmetoder', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 8, 1, NULL),
('radio', 43, 'Jag ger medarbetare positiv återkoppling', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 8, 1, NULL),
('radio', 44, 'Jag korrigerar medarbetares beteende vid behov', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 8, 1, NULL),
('radio', 45, 'Jag ger medarbetare negativ återkoppling', NULL, NULL, '{"values":{"5":"1","4":"2","3":"3","2":"4","1":"5"}}', 1, 8, 1, NULL),
('radio', 46, 'Jag kritiserar medarbetare', NULL, NULL, '{"values":{"5":"1","4":"2","3":"3","2":"4","1":"5"}}', 1, 8, 1, NULL),
('radio', 47, 'Jag löser konflikter i gruppen', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 8, 1, NULL),
('radio', 48, 'Jag har en tendens att lösa konflikter destruktivt', NULL, NULL, '{"values":{"5":"1","4":"2","3":"3","2":"4","1":"5"}}', 1, 8, 1, NULL),
('radio', 49, 'Jag har en tendens att styra i detalj hur medarbetare ska utföra sitt arbete', NULL, NULL, '{"values":{"5":"1","4":"2","3":"3","2":"4","1":"5"}}', 1, 8, 1, NULL),
('radio', 50, 'Min stöd till gruppen är verkningsfullt', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 8, 1, NULL),
('radio', 51, 'Om jag eller min grupp stöter på samarbetsproblem finns professionell hjälp att få', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 8, 1, NULL),

('radio', 52, 'Gruppens medlemmar är aktiva under våra möten', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 9, 1, NULL),
('radio', 53, 'Gruppens medlemmar bidrar aktivt till att förbättra arbetsmetoder', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 9, 1, NULL),
('radio', 54, 'Gruppens medlemmar uppmuntrar varandra i arbetet', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 9, 1, NULL),
('radio', 55, 'Gruppens medlemmar löser konflikter konstruktivt', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 9, 1, NULL),
('radio', 56, 'Vissa gruppmedlemmar vill bestämma över hur andra ska utföra sitt arbete', NULL, NULL, '{"values":{"5":"1","4":"2","3":"3","2":"4","1":"5"}}', 1, 9, 1, NULL),
('radio', 57, 'Det är lätt att fråga andra gruppmedlemmar till råds i arbetet', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 9, 1, NULL),
('radio', 58, 'Jag är nöjd med att vara med i denna grupp', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 9, 1, NULL),
('radio', 59, 'Jag tycker att arbetslaget fungerar bra i relation till arbetslagets mål och mening', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 9, 1, NULL),

('radio', 60, 'Jag tycker att frågorna i detta frågeformulär är relevanta', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 10, 1, NULL),
('radio', 61, 'Jag gjorde mitt bästa för att svara på frågorna i detta frågeformulär', NULL, NULL, '{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}', 1, 10, 1, NULL),
('textarea', 62, 'Övriga kommentarer', NULL, NULL, NULL, 1, 10, 0, NULL);

# Tydliga gränser
UPDATE elements SET assessmentCategoryId=8 WHERE sequence IN (10);

# Stabilitet
UPDATE elements SET assessmentCategoryId=9 WHERE sequence IN (12,13,14);

# Inbördesberoende
UPDATE elements SET assessmentCategoryId=10 WHERE sequence IN (15,16);

# Tydlig
UPDATE elements SET assessmentCategoryId=11 WHERE sequence IN (28,30);

# Utmanande
UPDATE elements SET assessmentCategoryId=12 WHERE sequence IN (29);

# Betydelsefulla
UPDATE elements SET assessmentCategoryId=13 WHERE sequence IN (31);

# Ledaren aktiv i att fastställa mål
UPDATE elements SET assessmentCategoryId=14 WHERE sequence IN (27);

# Gruppen aktiv i att utforska tillvägagångsättet
#UPDATE elements SET assessmentCategoryId=15 WHERE sequence IN ();

# Gruppensammansättning
UPDATE elements SET assessmentCategoryId=16 WHERE sequence IN (17,18,19,22);

# Beteendenormer
UPDATE elements SET assessmentCategoryId=17 WHERE sequence IN (20,21);

# Uppgiften är rätt utformad
UPDATE elements SET assessmentCategoryId=18 WHERE sequence IN (23,24,25,26);

# Material
UPDATE elements SET assessmentCategoryId=19 WHERE sequence IN (32);

# Kompetensutveckling/konsult
UPDATE elements SET assessmentCategoryId=20 WHERE sequence IN (33,34);

# Information
UPDATE elements SET assessmentCategoryId=33 WHERE sequence IN (35);

# Rätt belöning och erkännande
UPDATE elements SET assessmentCategoryId=21 WHERE sequence IN (36,37);

# Ledaren fokus
UPDATE elements SET assessmentCategoryId=22 WHERE sequence IN (38,39,40,41);

# Lämpligt innehåll i stödet
UPDATE elements SET assessmentCategoryId=23 WHERE sequence IN (42,43,44,45,46,47,48,49);

# Medarbertarnas stöd
    UPDATE elements SET assessmentCategoryId=26 WHERE sequence IN (52,53,54,55,56,57);

# Verkansgrad för ledarens coaching
UPDATE elements SET assessmentCategoryId=27 WHERE sequence IN (50);

# Tillgäng till stöd från experter
UPDATE elements SET assessmentCategoryId=28 WHERE sequence IN (51);

# Allmän trivsel i gruppen
UPDATE elements SET assessmentCategoryId=29 WHERE sequence IN (58);

# Gruppens alläman fungerande
UPDATE elements SET assessmentCategoryId=30 WHERE sequence IN (59);

# Face validity
UPDATE elements SET assessmentCategoryId=31 WHERE sequence IN (60);

# Attityder kring formuläret
UPDATE elements SET assessmentCategoryId=32 WHERE sequence IN (61);