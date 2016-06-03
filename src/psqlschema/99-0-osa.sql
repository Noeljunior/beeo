-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                     this is base schema for use
--                        with OSA use-case. This
--                   includes the base configuration
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

BEGIN;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ROLES
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO role (name, description, letter)
    VALUES ('admin',   'Administrador', 'A'),
           ('fakeadmin',  'Fake admin', 'F'),
           ('doctor',  'Médico', 'M'),
           ('labtech', 'Técnico de Laboratório', 'T'),
           ('guest',   'Visitante', 'V');


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   USERACCOUNT
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO useraccount (username, password, name, email, phone, mobile, url, info)
    VALUES ('admin', '', 'Ana Silva', 'asilva@email.com', '291234567', '951236589', 'linkedin.com/asilva', 'Técnica do INSA com permissões de administradora da plataforma'),
           ('doctor', '', 'José Figueiredo', 'jfigueiredo@email.com', '298452169', '958741259', 'linkedin.com/jfigueiredo', 'Médico do INSA'),
           ('labtech', '', 'Maria Domingues', 'mdomingues@email.com', '296589924', '953598425', 'linkedin.com/mdomingues', 'Técnica de laboratório do INSA'),
           ('admin2', '', 'Ana Costa', 'acosta@email.com', '291234567', '951236589', 'linkedin.com/acosta', 'Médica e técnica de laboratório do INSA'),
           ('doctor2', '', 'Carlos Rodrigues', 'crodrigues@email.com', '298452169', '958741259', 'linkedin.com/crodrigues', 'Médico do INSA'),
           ('labtech2', '', 'Mariana dos Anjos', 'manjos@email.com', '296589924', '953598425', 'linkedin.com/mdomingues', 'Técnica de laboratório do INSA'),
           ('fake', '', '', '', '', '', '', 'Para testes'),
           ('guest', '', 'António Amado', 'aamado@email.com', '295487963', '9532145698', 'linkedin.com/manjos', 'Médica do Hospital a participar no estudo'),
           ('miguelg', '', 'Miguel Gonçalves', '', '', '', '', ''),
           ('tiagof', '', 'Tiago de Faria', '', '', '', '', ''),
           ('tiagol', '', 'Tiago Levita', '', '', '', '', ''),
           ('dianag', '', 'Diana Gonçalves', '', '', '', '', '')
           ;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   USERROLES
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO userroles (userid, roleid)
    VALUES ((SELECT id FROM useraccount WHERE username = 'admin'),   (SELECT id FROM role WHERE name = 'admin')),
           ((SELECT id FROM useraccount WHERE username = 'admin'),   (SELECT id FROM role WHERE name = 'doctor')),
           ((SELECT id FROM useraccount WHERE username = 'admin'),   (SELECT id FROM role WHERE name = 'labtech')),
           ((SELECT id FROM useraccount WHERE username = 'doctor'),  (SELECT id FROM role WHERE name = 'doctor')),
           ((SELECT id FROM useraccount WHERE username = 'labtech'), (SELECT id FROM role WHERE name = 'labtech')),
           ((SELECT id FROM useraccount WHERE username = 'guest'),   (SELECT id FROM role WHERE name = 'guest')),
           ((SELECT id FROM useraccount WHERE username = 'fake'),    (SELECT id FROM role WHERE name = 'fakeadmin')),
           ((SELECT id FROM useraccount WHERE username = 'admin2'),  (SELECT id FROM role WHERE name = 'admin')),
           ((SELECT id FROM useraccount WHERE username = 'admin2'),  (SELECT id FROM role WHERE name = 'doctor')),
           ((SELECT id FROM useraccount WHERE username = 'doctor2'), (SELECT id FROM role WHERE name = 'doctor')),
           ((SELECT id FROM useraccount WHERE username = 'labtech2'),(SELECT id FROM role WHERE name = 'labtech')),
           ((SELECT id FROM useraccount WHERE username = 'miguelg'), (SELECT id FROM role WHERE name = 'admin')),
           ((SELECT id FROM useraccount WHERE username = 'miguelg'), (SELECT id FROM role WHERE name = 'doctor')),
           ((SELECT id FROM useraccount WHERE username = 'miguelg'), (SELECT id FROM role WHERE name = 'labtech')),
           ((SELECT id FROM useraccount WHERE username = 'miguelg'), (SELECT id FROM role WHERE name = 'guest')),
           ((SELECT id FROM useraccount WHERE username = 'tiagof'),  (SELECT id FROM role WHERE name = 'admin')),
           ((SELECT id FROM useraccount WHERE username = 'tiagof'),  (SELECT id FROM role WHERE name = 'doctor')),
           ((SELECT id FROM useraccount WHERE username = 'tiagof'),  (SELECT id FROM role WHERE name = 'labtech')),
           ((SELECT id FROM useraccount WHERE username = 'tiagof'),  (SELECT id FROM role WHERE name = 'guest')),
           ((SELECT id FROM useraccount WHERE username = 'tiagol'),  (SELECT id FROM role WHERE name = 'admin')),
           ((SELECT id FROM useraccount WHERE username = 'tiagol'),  (SELECT id FROM role WHERE name = 'doctor')),
           ((SELECT id FROM useraccount WHERE username = 'tiagol'),  (SELECT id FROM role WHERE name = 'labtech')),
           ((SELECT id FROM useraccount WHERE username = 'tiagol'),  (SELECT id FROM role WHERE name = 'guest')),
           ((SELECT id FROM useraccount WHERE username = 'dianag'),  (SELECT id FROM role WHERE name = 'admin')),
           ((SELECT id FROM useraccount WHERE username = 'dianag'),  (SELECT id FROM role WHERE name = 'doctor')),
           ((SELECT id FROM useraccount WHERE username = 'dianag'),  (SELECT id FROM role WHERE name = 'labtech')),
           ((SELECT id FROM useraccount WHERE username = 'dianag'),  (SELECT id FROM role WHERE name = 'guest'))
           ;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   PERMISSIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO permissions (roleid, module, mode )
    VALUES (0, 'uim_usermanagement', 'dv'),
           (0, 'uim_usermanagement', 'edit'),
           (0, 'uim_usermanagement', 'add'),
           (0, 'uim_usermanagement', 'view'),
           (0, 'uim_usermanagement', 'delete'),
           (0, 'uim_usermanagement', 'respasswd'),
           (0, 'uim_usermanagement', 'viewl'),
           (0, 'uim_usermanagement', 'viewodv'),
           (0, 'uim_usermanagement', 'viewo'),
           (0, 'uim_usermanagement', 'viewoedit'),
           (0, 'uim_usermanagement', 'viewoadd'),
           (0, 'uim_usermanagement', 'viewodel'),
           (0, 'uim_organizations', 'dv'),
           (0, 'uim_organizations', 'edit'),
           (0, 'uim_organizations', 'add'),
           (0, 'uim_organizations', 'view'),
           (0, 'uim_organizations', 'delete'),
           (0, 'uim_organizations', 'viewdv'),
           (0, 'uim_organizations', 'viewu'),
           (0, 'uim_organizations', 'viewuedit'),
           (0, 'uim_organizations', 'viewuadd'),
           (0, 'uim_organizations', 'viewudel'),
           (0, 'uim_questions', 'dv'),
           (0, 'uim_questions', 'manage'),
           (0, 'uim_events', 'dv'),
           (0, 'uim_events', 'manage'),
           (0, 'uim_activities', 'dv'),
           (0, 'uim_activities', 'manage'),
           (0, 'uim_patients', 'dv'),
           (0, 'uim_patients', 'search'),
           (0, 'uim_patients', 'add'),
           (0, 'uim_patients', 'list'),
           (0, 'uim_patients', 'view'),
           (0, 'uim_patients', 'manage'),
           
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_sysadmin', 'dv'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'dv'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'edit'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'add'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'view'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'delete'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'respasswd'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'viewl'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'viewodv'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'viewo'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'viewoedit'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'viewoadd'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_usermanagement', 'viewodel'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_organizations', 'dv'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_organizations', 'edit'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_organizations', 'add'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_organizations', 'view'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_organizations', 'delete'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_organizations', 'viewdv'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_organizations', 'viewu'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_organizations', 'viewuedit'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_organizations', 'viewuadd'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_organizations', 'viewudel'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_questions', 'dv'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_questions', 'manage'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_events', 'dv'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_events', 'manage'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_activities', 'dv'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_activities', 'manage'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_patients', 'dv'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_patients', 'search'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_patients', 'add'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_patients', 'list'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_patients', 'view'),
           ((SELECT id FROM role WHERE name = 'fakeadmin'), 'uim_patients', 'manage'),
            
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'dv'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'edit'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'add'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'view'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'delete'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'respasswd'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'viewl'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'viewodv'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'viewo'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'viewoedit'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'viewoadd'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_usermanagement', 'viewodel'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_organizations', 'dv'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_organizations', 'edit'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_organizations', 'add'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_organizations', 'view'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_organizations', 'delete'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_organizations', 'viewdv'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_organizations', 'viewu'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_organizations', 'viewuedit'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_organizations', 'viewuadd'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_organizations', 'viewudel'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_questions', 'dv'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_questions', 'manage'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_events', 'dv'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_events', 'manage'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_activities', 'dv'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_activities', 'manage'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_dbdump', 'dv'),
           ((SELECT id FROM role WHERE name = 'admin'), 'uim_dbdump', 'download'),
           
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_usermanagement', 'dv'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_usermanagement', 'view'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_usermanagement', 'viewodv'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_usermanagement', 'viewo'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_organizations', 'dv'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_organizations', 'view'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_organizations', 'viewdv'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_organizations', 'viewu'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_patients', 'dv'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_patients', 'search'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_patients', 'add'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_patients', 'view'),
           ((SELECT id FROM role WHERE name = 'doctor'), 'uim_patients', 'manage'),
           
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_usermanagement', 'dv'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_usermanagement', 'view'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_usermanagement', 'viewodv'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_usermanagement', 'viewo'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_organizations', 'dv'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_organizations', 'view'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_organizations', 'viewdv'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_organizations', 'viewu'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_patients', 'dv'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_patients', 'search'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_patients', 'add'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_patients', 'view'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_patients', 'manage'),
           ((SELECT id FROM role WHERE name = 'labtech'), 'uim_patients', 'list');


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ORGANIZATIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO organization (name, address, postcode, location, email, phone, fax, url, info)
    VALUES ('INSA', 'Rua das Palmeiras, 23', '1000', 'Lisboa', 'geral@insa.pt', '212121212', '212121212', 'www.insa.pt', 'INSA'),
           ('INSA LAB', 'Rua das Palmeiras, 23', '1000', 'Lisboa', 'geral@insa.pt', '212173737', '212173737', 'www.insa.pt', 'INSA'),
           ('Hospital de Santa Maria', 'Rua do Hospital, 1', '1000', 'Lisboa', 'geral@hsm.pt', '212121212',
                                '212121212', 'www.hsm.pt', 'Hospital de Santa Maria'),
           ('Outro local de investigação', 'Algures, 1', '1000', 'Lisboa', 'geral@oli.pt', '21000000', '210000000', 'www.oli.pt', 'Concorrência'),
           ('Bee-O', 'Rua da Felicidade', '666', 'Wonderland', 'geral@bee-o.pt', '', '', 'www.bee-o.pt', 'Bee Lovers'),
           ('Centro de Saúde', 'Rua do Centro de Saúde', '1000', 'Lisboa', 'geral@cs.pt', '', '', 'www.cs.pt', 'Centro de Saúde')
           ;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   USERORGANIZATIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO userorganization (userid, organizationid, function, email)
    VALUES ((SELECT id FROM useraccount WHERE username = 'admin'), (SELECT id FROM organization WHERE name = 'INSA'),
                                'Secretária', 'asilva@insa.pt'),
           ((SELECT id FROM useraccount WHERE username = 'admin'), (SELECT id FROM organization WHERE name = 'INSA LAB'),
                                'Secretária', 'asilva@insa.pt'),
           ((SELECT id FROM useraccount WHERE username = 'doctor'), (SELECT id FROM organization WHERE name = 'INSA'),
                                'Médico participante', 'jfigueiredo@insa.pt'),
           ((SELECT id FROM useraccount WHERE username = 'doctor'), (SELECT id FROM organization WHERE name = 'Hospital de Santa Maria'),
                                'Médico', 'jfigueiredo@hsm.pt'),
           ((SELECT id FROM useraccount WHERE username = 'labtech'), (SELECT id FROM organization WHERE name = 'INSA LAB'),
                                'Técnica de laboratório', 'mdomingues@insa.pt'),
           ((SELECT id FROM useraccount WHERE username = 'guest'), (SELECT id FROM organization WHERE name = 'Outro local de investigação'),
                                'Desconhecido', 'aamado@oli.pt'),
           ((SELECT id FROM useraccount WHERE username = 'admin2'), (SELECT id FROM organization WHERE name = 'INSA'),
                                'Responsável da Investigação', 'acosta@insa.pt'),
           ((SELECT id FROM useraccount WHERE username = 'admin2'), (SELECT id FROM organization WHERE name = 'Centro de Saúde'),
                                'Médica', 'acosta@cs.pt'),
           ((SELECT id FROM useraccount WHERE username = 'doctor2'), (SELECT id FROM organization WHERE name = 'INSA'),
                                'Médico', 'crodrigues@insa.pt'),
           ((SELECT id FROM useraccount WHERE username = 'doctor2'), (SELECT id FROM organization WHERE name = 'Hospital de Santa Maria'),
                                'Médico', 'crodrigues@hsm.pt'),
           ((SELECT id FROM useraccount WHERE username = 'labtech2'), (SELECT id FROM organization WHERE name = 'INSA LAB'),
                                'Técnica de laboratório', 'manjos@insa.pt'),
           ((SELECT id FROM useraccount WHERE username = 'miguelg'), (SELECT id FROM organization WHERE name = 'Bee-O'),
                                'Bee Lover', 'miguelg@bee-o.pt'),
           ((SELECT id FROM useraccount WHERE username = 'tiagof'), (SELECT id FROM organization WHERE name = 'Bee-O'),
                                'Bee Lover', 'tiagof@bee-o.pt'),
           ((SELECT id FROM useraccount WHERE username = 'tiagol'), (SELECT id FROM organization WHERE name = 'Bee-O'),
                                'Bee Lover', 'tiagol@bee-o.pt'),
           ((SELECT id FROM useraccount WHERE username = 'dianag'), (SELECT id FROM organization WHERE name = 'Bee-O'),
                                'Bee Lover', 'dianag@bee-o.pt')
                                ;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   PATIENT
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO patient (identifier, userid)
    VALUES ('pat1', (SELECT id FROM useraccount WHERE username = 'doctor')),
           ('pat2', (SELECT id FROM useraccount WHERE username = 'doctor2'))
           ;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   EVENTTYPE
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO eventtype (name, description)
    VALUES ('Consulta', 'Consulta médica'),
           ('Fase de tratamento', 'Fase do Tratamento'),
           ('Declaração do laboratório', 'Declaração de resultados do laboratório')

           --('1ª Consulta', 'Primeira consulta de patologia do sono'),
           --('Consulta de acompanhamento', 'Consulta de acompanhamento'),
           --'Análise', 'Declaração de resultado de análise'),
           --('Resultados', 'Declaração de resultado de exame')
           ;
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   EVENT
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO event (name, description, eventtypeid)
    VALUES
           ('1ª Consulta', 'Primeira consulta de patologia do sono',        (SELECT id FROM eventtype WHERE name = 'Consulta')),
           ('Consulta de acompanhamento', 'Consulta de acompanhamento',     (SELECT id FROM eventtype WHERE name = 'Consulta')),
           ('Pedido de amostras', 'Declaração de pedido de amostras',       (SELECT id FROM eventtype WHERE name = 'Declaração do laboratório')),
           ('Resultados', 'Declaração de resultados',                       (SELECT id FROM eventtype WHERE name = 'Declaração do laboratório')),
           ('Fase 1', 'Fase 1 do Tratamento',                               (SELECT id FROM eventtype WHERE name = 'Fase de tratamento')),
           ('Fase 2', 'Fase 2 do Tratamento',                               (SELECT id FROM eventtype WHERE name = 'Fase de tratamento'))
           ;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   INPUTITEM
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO inputitem (name, description)
    VALUES ('Sim', 'Sim'),
           ('Não', 'Não'),
           ('Não sei', 'Não sei'),
           ('Outro', 'Outro'),

--     Dados do paciente
           ('Masculino', 'Masculino'),
           ('Feminino', 'Feminino'),
           ('Até 4ª classe', 'Até 4ª classe'),
           ('Liceu', 'Liceu'),
           ('Faculdade', 'Faculdade'),
           ('Ligeiros', 'Ligeiros'),
           ('Pesados', 'Pesados'),
           ('Motociclos', 'Motociclos'),
           ('Profissional', 'Profissional'),
           ('Fumador', 'Fumador'),
           ('Não fumador', 'Não fumador'),
           ('Ex-fumador', 'Ex-fumador'),

--     Exame ORL
           ('Normal', 'Normal'),
           ('Anormal', 'Anormal'),
           ('Laxa', 'Laxa'),
           ('Alto', 'Alto'),
           ('Duvidoso', 'Duvidoso'),
           ('Pós-UPPP', 'Pós-uvulopalatoplastia'),
           ('Classe 1', 'Classe 1'),
           ('Classe 2', 'Classe 2'),
           ('Classe 3', 'Classe 3'),
           ('Classe 4', 'Classe 4'),
           ('Micrognatia', 'Micrognatia'),
           ('Retrognatia', 'Retrognatia'),
           ('Passiva', 'Passiva'),
           ('Activa', 'Activa'),

--     Sintomas
           ('Má higiene do sono', 'Má higiene do sono'),
           ('Insónia', 'Insónia'),
           ('Narcolepsia', 'Narcolepsia'),
           ('Síndrome pernas inquietas', 'Síndrome pernas inquietas'),
           ('Doença psiquiátrica', 'Doença psiquiátrica'),
           ('Roncopatia simples', 'Roncopatia simples'),
           ('SAOS', 'Síndrome da apneia obstrutiva do sono'),
           ('Obstrução nasal', 'Obstrução nasal'),
           ('Marcas/lesões na face', 'Marcas/lesões na face'),
           ('Rinorreia', 'Rinorreia'),
           ('Olhos vermelhos', 'Olhos vermelhos'),
           ('Distensão gástrica', 'Distensão gástrica'),
           ('Secura das mucosas', 'Secura das mucosas'),
           ('Otalgia', 'Otalgia'),
           ('Ruído', 'Ruído'),
           ('Desconforto', 'Desconforto'),
           ('Claustrofobia', 'Claustrofobia'),
           ('Intolerância à pressão', 'Intolerância à pressão'),

--     Probabilidade de adormecer
           ('Nenhuma', 'Nenhuma'),
           ('Ligeira', 'Ligeira'),
           ('Moderada', 'Moderada'),
           ('Forte', 'Forte'),

--     Problemas médicos
           ('Enfarte', 'Enfarte'),
           ('Asma', 'Asma'),
           ('Diabetes', 'Diabetes'),
           ('Arritmia', 'Arritmia'),
           ('Angina de peito', 'Angina de peito'),
           ('Hiperuricémia', 'Ácido úrico elevado'),
           ('Dislipidémia', 'Colesterol/triglicéridos altos'),
           ('Bronquite crónica', 'Bronquite crónica'),
           ('Doenças da tiróide', 'Doenças da tiróide'),
           ('Impotência sexual', 'Impotência sexual'),
           ('Insuficiência respiratória', 'Insuficiência respiratória'),
           ('HTA', 'Hipertensão arterial'),
           ('Depressão', 'Depressão'),
           ('AVC/AIT', 'Acidente vascular cerebral / Ataque isquémico transitório'),
           ('Cardiopatia isquémica', 'Cardiopatia isquémica'),
           ('Insuficiência cardíaca', 'Insuficiência cardíaca'),
           ('DPOC', 'Doença pulmonar obstrutiva crónica'),

--      Exames médicos
           ('Polissonografia', 'Polissonografia'),
           ('Teste de latência múltipla do sono', 'Teste de latência múltipla do sono'),
           ('Teste de manutenção da vigília', 'Teste de manutenção da vigília'),
           ('Estudo funcional respiratório', 'Estudo funcional respiratório'),
           ('Análises da função tiroideia', 'Análises da função tiroideia'),
           ('Gasometria arterial', 'Gasometria arterial'),
           ('TAC craneo-encefálica', 'TAC craneo-encefálica'),

--      Resultado de exame
           ('Laboratório', 'Laboratório'),
           ('Domicílio', 'Domicílio'),
           ('Internamento', 'Internamento'),
           ('Obstrutivo', 'Obstrutivo'),
           ('Restritivo', 'Restritivo'),
           ('Misto', 'Misto'),
           ('Alterada', 'Alterada'),

--      Tratamento
           ('Corticoide nasal', 'Corticoide nasal');


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                             inputtypes
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO inputtype (name, description, options)
    VALUES ('Boolean', 'Resposta de Sim e Não', 'exclusive'),

--  Exame ORL
           ('Palato', 'Resposta sobre o palato', 'exclusive'),
           ('Orofaringe', 'Resposta sobre a orofaringe', 'exclusive'),
           ('Mallampati', 'Resposta sobre o mallampati', 'exclusive'),
           ('Articulação temporo-mandibular', 'Resposta sobre a Articulação temporo-mandibular', 'exclusive'),
           ('Posição do mento', 'Resposta sobre a Posição do mento', 'exclusive'),
           ('Manobra inspiratória nasal', 'Resposta sobre a Manobra inspiratória nasal', 'exclusive'),

--  Decisão clínica
           ('Sonolência diurna', 'Resposta sobre Sonolência diurna', 'exclusive'),
           ('Impressão clínica', 'Resposta sobre Impressão clínica', ''),

--  Primeiro questionário
           ('Sexo', 'Sexo', 'exclusive'),
           ('Escolaridade', 'Escolaridade', 'exclusive'),
           ('Carta de condução', 'Carta de condução', 'exclusive'),
           ('Historial de doenças', 'Historial de doenças', ''),
           ('Probabilidade de adormecer', 'Probabilidade de adormecer', 'exclusive')

           ;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                             inputtypeinputitem
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --


INSERT INTO inputtypeinputitem (inputitemid, inputtypeid)
VALUES ((SELECT id FROM inputitem  WHERE name = 'Não'),
        (SELECT id FROM inputtype WHERE name = 'Boolean')),
       ((SELECT id FROM inputitem  WHERE name = 'Sim'),
        (SELECT id FROM inputtype WHERE name = 'Boolean')),

--  Palato
       ((SELECT id FROM inputitem  WHERE name = 'Normal'),
        (SELECT id FROM inputtype WHERE name = 'Palato')),
       ((SELECT id FROM inputitem  WHERE name = 'Alto'),
        (SELECT id FROM inputtype WHERE name = 'Palato')),
       ((SELECT id FROM inputitem  WHERE name = 'Duvidoso'),
        (SELECT id FROM inputtype WHERE name = 'Palato')),
       ((SELECT id FROM inputitem  WHERE name = 'Pós-UPPP'),
        (SELECT id FROM inputtype WHERE name = 'Palato')),

--  Orofaringe
       ((SELECT id FROM inputitem  WHERE name = 'Normal'),
        (SELECT id FROM inputtype WHERE name = 'Orofaringe')),
       ((SELECT id FROM inputitem  WHERE name = 'Anormal'),
        (SELECT id FROM inputtype WHERE name = 'Orofaringe')),

--  Mallampati
       ((SELECT id FROM inputitem  WHERE name = 'Classe 1'),
        (SELECT id FROM inputtype WHERE name = 'Mallampati')),
       ((SELECT id FROM inputitem  WHERE name = 'Classe 2'),
        (SELECT id FROM inputtype WHERE name = 'Mallampati')),
       ((SELECT id FROM inputitem  WHERE name = 'Classe 3'),
        (SELECT id FROM inputtype WHERE name = 'Mallampati')),
       ((SELECT id FROM inputitem  WHERE name = 'Classe 4'),
        (SELECT id FROM inputtype WHERE name = 'Mallampati')),

--  Articulação temporo-mandibular
       ((SELECT id FROM inputitem  WHERE name = 'Normal'),
        (SELECT id FROM inputtype WHERE name = 'Articulação temporo-mandibular')),
       ((SELECT id FROM inputitem  WHERE name = 'Laxa'),
        (SELECT id FROM inputtype WHERE name = 'Articulação temporo-mandibular')),

--  Posição do mento
       ((SELECT id FROM inputitem  WHERE name = 'Normal'),
        (SELECT id FROM inputtype WHERE name = 'Posição do mento')),
       ((SELECT id FROM inputitem  WHERE name = 'Micrognatia'),
        (SELECT id FROM inputtype WHERE name = 'Posição do mento')),
       ((SELECT id FROM inputitem  WHERE name = 'Retrognatia'),
        (SELECT id FROM inputtype WHERE name = 'Posição do mento')),

--  Manobra inspiratória nasal
       ((SELECT id FROM inputitem  WHERE name = 'Normal'),
        (SELECT id FROM inputtype WHERE name = 'Manobra inspiratória nasal')),
       ((SELECT id FROM inputitem  WHERE name = 'Anormal'),
        (SELECT id FROM inputtype WHERE name = 'Manobra inspiratória nasal')),

--  Sonolência diurna
       ((SELECT id FROM inputitem  WHERE name = 'Não'),
        (SELECT id FROM inputtype WHERE name = 'Sonolência diurna')),
       ((SELECT id FROM inputitem  WHERE name = 'Passiva'),
        (SELECT id FROM inputtype WHERE name = 'Sonolência diurna')),
       ((SELECT id FROM inputitem  WHERE name = 'Activa'),
        (SELECT id FROM inputtype WHERE name = 'Sonolência diurna')),

--  Impressão clínica
       ((SELECT id FROM inputitem  WHERE name = 'Má higiene do sono'),
        (SELECT id FROM inputtype WHERE name = 'Impressão clínica')),
       ((SELECT id FROM inputitem  WHERE name = 'Insónia'),
        (SELECT id FROM inputtype WHERE name = 'Impressão clínica')),
       ((SELECT id FROM inputitem  WHERE name = 'Narcolepsia'),
        (SELECT id FROM inputtype WHERE name = 'Impressão clínica')),
       ((SELECT id FROM inputitem  WHERE name = 'Síndrome pernas inquietas'),
        (SELECT id FROM inputtype WHERE name = 'Impressão clínica')),
       ((SELECT id FROM inputitem  WHERE name = 'Doença psiquiátrica'),
        (SELECT id FROM inputtype WHERE name = 'Impressão clínica')),
       ((SELECT id FROM inputitem  WHERE name = 'Roncopatia simples'),
        (SELECT id FROM inputtype WHERE name = 'Impressão clínica')),
       ((SELECT id FROM inputitem  WHERE name = 'SAOS'),
        (SELECT id FROM inputtype WHERE name = 'Impressão clínica')),

--  Sexo
       ((SELECT id FROM inputitem  WHERE name = 'Feminino'),
        (SELECT id FROM inputtype WHERE name = 'Sexo')),
       ((SELECT id FROM inputitem  WHERE name = 'Masculino'),
        (SELECT id FROM inputtype WHERE name = 'Sexo')),
       ((SELECT id FROM inputitem  WHERE name = 'Outro'),
        (SELECT id FROM inputtype WHERE name = 'Sexo')),

--  Escolaridade
       ((SELECT id FROM inputitem  WHERE name = 'Até 4ª classe'),
        (SELECT id FROM inputtype WHERE name = 'Escolaridade')),
       ((SELECT id FROM inputitem  WHERE name = 'Liceu'),
        (SELECT id FROM inputtype WHERE name = 'Escolaridade')),
       ((SELECT id FROM inputitem  WHERE name = 'Faculdade'),
        (SELECT id FROM inputtype WHERE name = 'Escolaridade')),

--  Carta de condução
       ((SELECT id FROM inputitem  WHERE name = 'Ligeiros'),
        (SELECT id FROM inputtype WHERE name = 'Carta de condução')),
       ((SELECT id FROM inputitem  WHERE name = 'Pesados'),
        (SELECT id FROM inputtype WHERE name = 'Carta de condução')),
       ((SELECT id FROM inputitem  WHERE name = 'Motociclos'),
        (SELECT id FROM inputtype WHERE name = 'Carta de condução')),
       ((SELECT id FROM inputitem  WHERE name = 'Profissional'),
        (SELECT id FROM inputtype WHERE name = 'Carta de condução')),

--  Historial de doenças
       ((SELECT id FROM inputitem  WHERE name = 'Enfarte'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),
       ((SELECT id FROM inputitem  WHERE name = 'Asma'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),
       ((SELECT id FROM inputitem  WHERE name = 'Diabetes'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),
       ((SELECT id FROM inputitem  WHERE name = 'Arritmia'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),
       ((SELECT id FROM inputitem  WHERE name = 'Angina de peito'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),
       ((SELECT id FROM inputitem  WHERE name = 'Hiperuricémia'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),
       ((SELECT id FROM inputitem  WHERE name = 'Dislipidémia'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),
       ((SELECT id FROM inputitem  WHERE name = 'Bronquite crónica'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),
       ((SELECT id FROM inputitem  WHERE name = 'Doenças da tiróide'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),
       ((SELECT id FROM inputitem  WHERE name = 'Impotência sexual'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),
       ((SELECT id FROM inputitem  WHERE name = 'AVC/AIT'),
        (SELECT id FROM inputtype WHERE name = 'Historial de doenças')),

--  Probabilidade de adormecer
       ((SELECT id FROM inputitem  WHERE name = 'Nenhuma'),
        (SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer')),
       ((SELECT id FROM inputitem  WHERE name = 'Ligeira'),
        (SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer')),
       ((SELECT id FROM inputitem  WHERE name = 'Moderada'),
        (SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer')),
       ((SELECT id FROM inputitem  WHERE name = 'Forte'),
        (SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer'))

       
        ;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   SEPARATORS & Number
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --


INSERT INTO question (inputtypeid, name, question)
    VALUES

--  Separadores
           ('6', 'Separador observações', 'Observações'),
           ('6', 'Separador observação', 'Observação'),
           ('6', 'Separador exame ORL', 'Exame ORL'),
           ('6', 'Separador exames complementares efectuados anteriormente', 'Exames complementares efectuados anteriormente'),
           ('6', 'Separador decisão clínica', 'Decisão clínica'),
           ('6', 'Separador impressão clínica', 'Impressão clínica'),
           ('6', 'Separador exames complementares solicitados', 'Exames complementares solicitados'),
           ('6', 'Separador probabilidade de adormecer', 'Qual a probabilidade de adormecer nestas situações?'),
           ('6', 'Separador exames complementares de diagnóstico', 'Exames complementares de diagnóstico'),
           ('6', 'Separador estudo do sono', 'Estudo do sono'),
           ('6', 'Separador análise da função tiroideia', 'Análise da função tiroideia'),
           ('6', 'Separador estudo funcional respiratório', 'Estudo funcional respiratório'),
           ('6', 'Separador gasometria arterial', 'Gasometria arterial'),
           ('6', 'Separador TAC craneo-encefálica', 'TAC craneo-encefálica'),
           ('6', 'Separador plano de tratamento', 'Plano de tratamento'),
           ('6', 'Separador consultas de apoio', 'Consultas de apoio'),
           ('6', 'Separador outros exames a solicitar', 'Outros exames a solicitar'),
           ('6', 'Separador diagnóstico principal', 'Diagnóstico principal'),
           ('6', 'Separador equipamento', 'Equipamento'),
           ('6', 'Separador parametrização', 'Parametrização'),
           ('6', 'Separador adesão', 'Adesão'),
           ('6', 'Separador eficácia', 'Eficácia'),
           ('6', 'Separador sintomatologia/complicações', 'Sintomatologia/Complicações'),
           ('6', 'Separador alterações instituídas', 'Alterações instituídas'),
           ('6', 'Separador plano terapêutico', 'Plano terapêutico'),
           ('6', 'Separador questionário de conhecimentos', 'Questionário de conhecimentos'),
           ('6', 'Separador queixa mais importante', 'Queixa mais importante'),
           ('6', 'Separador horas de sono', 'Hábitos de sono'),
           ('6', 'Separador estilo de vida', 'Estilo de vida'),
           ('6', 'Separador outros problemas de saúde', 'Outros problemas de saúde'),
           ('6', 'Separador medicamentação', 'Medicamentação'),
           ('6', 'Separador hábitos de sono', 'Hábitos de sono'),
           ('6', 'Separador outros hábitos de sono', 'Outros hábitos de sono'),

--  Números
           ('1', 'Idade', 'Idade'),
           ('1', 'IMC', 'IMC'),
           ('1', 'Altura', 'Altura (cm)'),
           ('1', 'Peso', 'Peso (kg)'),
           ('1', 'Tamanho do pescoço', 'Tamanho do pescoço'),
           ('1', 'Ritmo cardíaco', 'Ritmo cardíaco (bpm)'),
           ('1', 'Pressão arterial sistólica', 'Pressão arterial sistólica'),
           ('1', 'Pressão arterial diastólica', 'Pressão arterial diastólica'),
           ('1', 'Quantas vezes se levanta durante a noite para urinar?', 'Quantas vezes se levanta durante a noite para urinar?'),
           ('1', 'RDI', 'RDI (/h)'),
           ('1', 'IAH', 'IAH (/h)'),
           ('1', 'Saturação de O2 < 90%', 'Saturação de O2 < 90% (%)'),
           ('1', 'IPLMS', 'IPLMS (/h)'),
           ('1', 'Eficiência do sono', 'Eficiência do sono (%)'),
           ('1', 'Latência ao sono', 'Latência ao sono (/min)'),
           ('1', 'Índice de microdespertares', 'Índice de microdespertares (/h)'),
           ('1', 'Latência média ao sono', 'Latência média ao sono (/min)'),
           ('1', 'Número de sestas com REM', 'Número de sestas com REM'),
           ('1', 'PH', 'PH'),
           ('1', 'PaCO2', 'PaCO2 (mmHg)'),
           ('1', 'PaO2', 'PaO2 (mmHg)'),
           ('1', 'HCO3', 'HCO3'),
           ('1', 'SaO2', 'SaO2 (%)'),
           ('1', 'Número de cigarros por dia', 'Número de cigarros por dia'),

--  Time
           ('5', 'Horas de sono durante a semana', 'Durante a semana quantas horas dorme habitualmente durante a noite?'),
           ('5', 'Horas de sono durante o fim-de-semana', 'e nos fins-de-semana?'),

--  Data
           ('4', 'Data', 'Data'),
           ('4', 'Data da polissonografia com EEG', 'Polissonografia com EEG'),
           ('4', 'Data da gasometria arterial', 'Gasometria arterial'),
           ('4', 'Data da TC SPN/faringe', 'TC SPN/faringe'),
           ('4', 'Data da TC craneo-encefálica', 'TC craneo-encefálica'),
           ('4', 'Data da poligrafia cardiorespiratória', 'Poligrafia cardiorespiratória'),
           ('4', 'Data do teste latência múltipla', 'Teste latência múltipla'),
           ('4', 'Data do teste manutenção da vigília', 'Teste manutenção da vigília'),
           ('4', 'Data da análise da função tiroideia', 'Análise da função tiroideia'),
           ('4', 'Data do estudo funcional respiratório', 'Estudo funcional respiratório'),
           ('4', 'Data da polissonografia', 'Polissonografia'),
           ('4', 'Data da terapêutica com auto-CPAP nasal com adaptação', 'Terapêutica com auto-CPAP nasal com adaptação'),
           ('4', 'Data da consulta de nutrição', 'Consulta de nutrição'),
           ('4', 'Data da consulta de cessação tabágica', 'Consulta de cessação tabágica'),
           ('4', 'Data da consulta de ORL/roncopatia', 'Consulta de ORL/roncopatia'),
           ('4', 'Data da consulta de cirurgia-obesidade', 'Consulta de cirurgia-obesidade'),
           ('4', 'Data da consulta de neurologia', 'Consulta de neurologia'),
           ('4', 'Data da consulta de psiquiatria/psicologia', 'Consulta de psiquiatria/psicologia'),
           ('4', 'Data da outra consulta', 'Outra consulta'),
           ('4', 'Data da poligrafia cardiorespiratória com APAP', 'Poligrafia cardiorespiratória com APAP'),
           ('4', 'Data da polissonografia com APAP', 'Polissonografia com APAP'),
           ('4', 'Data da próxima consulta', 'Data da próxima consulta'),
           ('4', 'Data da próxima reavaliação', 'Data da próxima reavaliação'),

--  Short text
           ('2', 'IMC observações', 'IMC (Observações)'),
           ('2', 'Nome', 'Nome'),
           ('2', 'Profissão', 'Profissão'),
           ('2', 'Queixa mais importante', 'Qual a queixa mais importante que o trás à consulta?'),
           ('2', 'Medicamentos toma habitualmente', 'Que medicamentos toma habitualmente?'),
           ('2', 'Terapêutica farmacológica', 'Terapêutica farmacológica'),
           ('2', 'Cessação tabágica desde', 'Cessação tabágica desde'),
           ('2', 'Rampa', 'Rampa'),
           ('2', 'Corticoide nasal', 'Corticoide nasal'),
           ('2', 'Bromídio ipratrópio nasal', 'Bromídio ipratrópio nasal'),
           ('2', 'Anti-histamínico oral', 'Anti-histamínico oral'),
           ('2', 'Consultas de apoio', 'Consultas de apoio'),
           ('2', 'Autoavaliação - Nome da doença?', 'Qual o nome da sua doença?'),
           ('2', 'Autoavaliação - Principal sintoma', 'Qual o principal sintoma da sua doença?'),
           ('2', 'Autoavaliação - Nome do ventilador', 'Qual o nome do ventilador que utiliza?'),
           ('2', 'Autoavaliação - Número de lavagens da máscara', 'Quantas vezes lava a máscara por semana?'),
           ('2', 'Autoavaliação - Como lava a máscara', 'Como lava a sua máscara?'),
           ('2', 'Autoavaliação - Como resolve os problemas do ventilador', 'Como resolve os problemas do seu ventilador?'),
           ('2', 'Autoavaliação - Nariz entupido', 'O que faz se tiver o nariz entupido?'),
           ('2', 'Autoavaliação - Olhos vermelhos', 'O que faz se ficar com os olhos vermelhos?'),

--  Long text
           ('3', 'Plano de tratamento', 'Plano de tratamento'),
           ('3', 'Observações', 'Observações'),
           ('3', 'Exames complementares efectuados anteriormente', 'Exames complementares efectuados anteriormente'),

--  Personalizadas
           ((SELECT id FROM inputtype WHERE name = 'Palato'), 'Palato', 'Palato'),
           ((SELECT id FROM inputtype WHERE name = 'Orofaringe'), 'Orofaringe', 'Orofaringe'),
           ((SELECT id FROM inputtype WHERE name = 'Mallampati'), 'Mallampati', 'Mallampati'),
           ((SELECT id FROM inputtype WHERE name = 'Articulação temporo-mandibular'), 'Articulação temporo-mandibular',
                                'Articulação temporo-mandibular'),
           ((SELECT id FROM inputtype WHERE name = 'Posição do mento'), 'Posição do mento', 'Posição do mento'),
           ((SELECT id FROM inputtype WHERE name = 'Manobra inspiratória nasal'), 'Manobra inspiratória nasal', 'Manobra inspiratória nasal'),
           ((SELECT id FROM inputtype WHERE name = 'Sonolência diurna'), 'Sonolência diurna', 'Sonolência diurna'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Patologia cardio e/ou cerebrovascular', 'Patologia cardio e/ou cerebrovascular'),
           ((SELECT id FROM inputtype WHERE name = 'Impressão clínica'), 'Impressão clínica', 'Impressão clínica'),

           ((SELECT id FROM inputtype WHERE name = 'Sexo'), 'Sexo', 'Sexo'),
           ((SELECT id FROM inputtype WHERE name = 'Escolaridade'), 'Escolaridade', 'Escolaridade'),
           ((SELECT id FROM inputtype WHERE name = 'Carta de condução'), 'Carta de condução', 'Carta de condução'),
           ((SELECT id FROM inputtype WHERE name = 'Historial de doenças'), 'Historial de doenças', 'Historial de doenças'),

           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Trabalha por turnos?', 'Trabalha por turnos?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Custa-lhe a adormecer?', 'Custa-lhe a adormecer?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Acorda habitualmente durante a noite?',
                                            'Acorda habitualmente durante a noite?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Acorda mais cedo do que queria?',
                                            'Acorda mais cedo do que queria?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Toma habitualmente comprimidos para dormir?',
                                            'Toma habitualmente comprimidos para dormir?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Bebe regularmente bebidas alcoólicas?',
           'Bebe regularmente bebidas alcoólicas?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Bebe diariamente café, chá preto ou coca-cola?',
           'Bebe diariamente café, chá preto ou coca-cola?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Fuma ou já existiu algum período em que fumou?',
           'Fuma ou já existiu algum período em que fumou?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Já teve algum acidente por ter adormecido a conduzir?',
                                    'Já teve algum acidente por ter adormecido a conduzir?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Tem hipertensão (tensão alta)?',
           'Tem hipertensão (tensão alta)?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Tem ou já teve problemas do coração?',
           'Tem ou já teve problemas do coração?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Tem problemas do nariz (entupido, outros)?',
           'Tem problemas do nariz (entupido, outros)?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Já foi operado ao nariz ou garganta?',
           'Já foi operado ao nariz ou garganta?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Está deprimido?', 'Está deprimido?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Tem outras doenças?', 'Tem outras doenças?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Ressona durante o sono?', 'Ressona durante o sono?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Alguém lhe disse que pára de respirar durante o sono?',
                                'Alguém lhe disse que pára de respirar durante o sono?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Desperta durante a noite com sensação de asfixia?',
                                'Desperta durante a noite com sensação de asfixia?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Tem dores de cabeça quando se levanta de manhã?',
                                'Tem dores de cabeça quando se levanta de manhã?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Quando se levanta sente que não descansou à noite?',
                                'Quando se levanta sente que não descansou à noite?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Tem problemas de concentração ou pouca agilidade mental?',
                                'Tem problemas de concentração ou pouca agilidade mental?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Tem sonolência durante o dia?',
                                'Tem sonolência durante o dia?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Quando adormece tem sonhos ou pesadelos que parecem reais?',
                                'Quando adormece tem sonhos ou pesadelos que parecem reais?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Alguma vez teve sonhos ou pesadelos acordado?',
                                'Alguma vez teve sonhos ou pesadelos acordado?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Sem forças durante emoção',
                                'Durante uma emoção intensa alguma vez notou ficar sem forças em toda ou parte do corpo ou mesmo ter desmaiado?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Paralizado a dormir',
                                'Ficou alguma vez paralisado ou sem se poder mexer quando começou a dormir ou quando se levantou?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Mexe muito as pernas enquanto adormece ou a meio da noite?', 
                                'Mexe muito as pernas enquanto adormece ou a meio da noite?'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Se tem sonolência nota que esta melhora depois de uma sesta?', 
                                'Se tem sonolência nota que esta melhora depois de uma sesta?'),
          
           ((SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer'), 'Sentado e a ler', 'Sentado e a ler'),
           ((SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer'), 'A ver televisão', 'A ver televisão'),
           ((SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer'), 'Sentado em público',
                                'Sentado em público (concertos, teatros, sessões...)'),
           ((SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer'), 'Como passageiro em viagens', 'Como passageiro em viagens (1 hora)'),
           ((SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer'), 'Deitado a meio da tarde', 'Deitado a meio da tarde'),
           ((SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer'), 'Sentado a falar com alguém', 'Sentado a falar com alguém'),
           ((SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer'), 'Sentado após as refeições',
                                'Sentado tranquilamente após as refeições (sem beber álcool)'),
           ((SELECT id FROM inputtype WHERE name = 'Probabilidade de adormecer'), 'A conduzir', 'A conduzir (parado num semáforo ou em filas...)'),
           
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Efectuou tratamento', 'Efectuou tratamento'),
           ((SELECT id FROM inputtype WHERE name = 'Boolean'), 'Tratamento correu bem', 'Tratamento correu bem?')
          
          
          
           ;

UPDATE question SET description = name;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ActivityType
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO activitytype (name, description)
    VALUES ('Questionário',         'Questionário'),
           ('Tratamentos',          'Tratamentos'),
           ('Pedidos de exames',    'Pedidos de exames'),
           ('Resultados de exames', 'Pedidos')
           
           
           ;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   Activity
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO activity (name, description, activitytypeid)
    VALUES ('Registo da 1ª Consulta de Patologia do Sono', 'Primeira consulta de patologia do sono',
                                 (SELECT id FROM activitytype WHERE name = 'Questionário')),
           ('Recolha de Dados Biométricos', 'Questionário para recolha de dados biométricos',
                                 (SELECT id FROM activitytype WHERE name = 'Questionário')),
           ('Questionário ao paciente na 1ª Consulta', 'Questionário da Primeira Consulta de Patologia do Sono',
                                 (SELECT id FROM activitytype WHERE name = 'Questionário')),
-- Tratamentos
           ('Tratamento A', 'Tratamento A',
                                 (SELECT id FROM activitytype WHERE name = 'Tratamentos')),
           ('Tratamento B', 'Tratamento B',
                                 (SELECT id FROM activitytype WHERE name = 'Tratamentos')),
           ('Tratamento C', 'Tratamento C',
                                 (SELECT id FROM activitytype WHERE name = 'Tratamentos')),
           ('Tratamento D', 'Tratamento D',
                                 (SELECT id FROM activitytype WHERE name = 'Tratamentos'))
            ;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ActivityQuestion
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO activityquestion (activityid, questionid, precedence)
    VALUES

--  1ª Consulta de Patologia do Sono
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Separador observação'), 1),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'IMC'), 2),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'IMC observações'), 3),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Separador exame ORL'), 4),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Palato'), 5),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Orofaringe'), 6),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Mallampati'), 7),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Articulação temporo-mandibular'), 8),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Posição do mento'), 9),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Manobra inspiratória nasal'), 10),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Separador exames complementares efectuados anteriormente'), 11),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Exames complementares efectuados anteriormente'), 12),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Separador decisão clínica'), 13),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Sonolência diurna'), 14),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Patologia cardio e/ou cerebrovascular'), 15),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Separador impressão clínica'), 16),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Impressão clínica'), 17),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Separador exames complementares solicitados'), 18),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Data da polissonografia com EEG'), 19),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Data da gasometria arterial'), 20),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Data da TC SPN/faringe'), 21),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Data da TC craneo-encefálica'), 22),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Data da poligrafia cardiorespiratória'), 23),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Data do teste latência múltipla'), 24),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Data do teste manutenção da vigília'), 25),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Data da análise da função tiroideia'), 26),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Data do estudo funcional respiratório'), 27),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Separador observações'), 28),
           ((SELECT id FROM activity WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM question WHERE name = 'Observações'), 29),

--  Recolha de Dados Biométricos
           ((SELECT id FROM activity WHERE name = 'Recolha de Dados Biométricos'),
            (SELECT id FROM question WHERE name = 'Peso'), 1),
           ((SELECT id FROM activity WHERE name = 'Recolha de Dados Biométricos'),
            (SELECT id FROM question WHERE name = 'Altura'), 2),
           ((SELECT id FROM activity WHERE name = 'Recolha de Dados Biométricos'),
            (SELECT id FROM question WHERE name = 'IMC'), 3),
           ((SELECT id FROM activity WHERE name = 'Recolha de Dados Biométricos'),
            (SELECT id FROM question WHERE name = 'IMC observações'), 4),
           ((SELECT id FROM activity WHERE name = 'Recolha de Dados Biométricos'),
            (SELECT id FROM question WHERE name = 'Ritmo cardíaco'), 5),
           ((SELECT id FROM activity WHERE name = 'Recolha de Dados Biométricos'),
            (SELECT id FROM question WHERE name = 'Tamanho do pescoço'), 6),
           ((SELECT id FROM activity WHERE name = 'Recolha de Dados Biométricos'),
            (SELECT id FROM question WHERE name = 'Pressão arterial sistólica'), 7),
           ((SELECT id FROM activity WHERE name = 'Recolha de Dados Biométricos'),
            (SELECT id FROM question WHERE name = 'Pressão arterial diastólica'), 8),

--  Questionário da 1ª Consulta
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Nome'), 1),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Idade'), 2),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Profissão'), 3),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Sexo'), 4),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Peso'), 5),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Altura'), 6),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Tamanho do pescoço'), 7),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Escolaridade'), 8),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Carta de condução'), 9),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Separador queixa mais importante'), 10),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Queixa mais importante'), 11),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Separador horas de sono'), 12),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Horas de sono durante a semana'), 13),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Horas de sono durante o fim-de-semana'), 14),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Separador estilo de vida'), 15),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Trabalha por turnos?'), 16),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Custa-lhe a adormecer?'), 17),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Acorda habitualmente durante a noite?'), 18),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Acorda mais cedo do que queria?'), 19),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Toma habitualmente comprimidos para dormir?'), 20),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Bebe regularmente bebidas alcoólicas?'), 21),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Bebe diariamente café, chá preto ou coca-cola?'), 22),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Fuma ou já existiu algum período em que fumou?'), 23),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Já teve algum acidente por ter adormecido a conduzir?'), 24),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Separador outros problemas de saúde'), 25),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Tem hipertensão (tensão alta)?'), 26),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Tem ou já teve problemas do coração?'), 27),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Tem problemas do nariz (entupido, outros)?'), 28),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Já foi operado ao nariz ou garganta?'), 29),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Está deprimido?'), 30),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Tem outras doenças?'), 31),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Historial de doenças'), 32),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Separador medicamentação'), 33),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Medicamentos toma habitualmente'), 34),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Separador hábitos de sono'), 35),                              --Hábitos de sono
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Ressona durante o sono?'), 36),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Alguém lhe disse que pára de respirar durante o sono?'), 37),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Desperta durante a noite com sensação de asfixia?'), 38),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Tem dores de cabeça quando se levanta de manhã?'), 39),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Quando se levanta sente que não descansou à noite?'), 40),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Tem problemas de concentração ou pouca agilidade mental?'), 41),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Tem sonolência durante o dia?'), 42),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Quantas vezes se levanta durante a noite para urinar?'), 43),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Separador outros hábitos de sono'), 44),                              -- Outros hábitos de sono
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Quando adormece tem sonhos ou pesadelos que parecem reais?'), 45),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Alguma vez teve sonhos ou pesadelos acordado?'), 46),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Sem forças durante emoção'), 47),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Paralizado a dormir'), 48),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Mexe muito as pernas enquanto adormece ou a meio da noite?'), 49),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Se tem sonolência nota que esta melhora depois de uma sesta?'), 50),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Separador probabilidade de adormecer'), 51),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Sentado e a ler'), 52),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'A ver televisão'), 53),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Sentado em público'), 54),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Como passageiro em viagens'), 55),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Deitado a meio da tarde'), 56),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Sentado a falar com alguém'), 57),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'Sentado após as refeições'), 58),
           ((SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM question WHERE name = 'A conduzir'), 59),
            
           ((SELECT id FROM activity WHERE name = 'Tratamento A'),
            (SELECT id FROM question WHERE name = 'Efectuou tratamento'), 1),
           ((SELECT id FROM activity WHERE name = 'Tratamento A'),
            (SELECT id FROM question WHERE name = 'Tratamento correu bem'), 2),
           ((SELECT id FROM activity WHERE name = 'Tratamento A'),
            (SELECT id FROM question WHERE name = 'Observações'), 3),
            
           ((SELECT id FROM activity WHERE name = 'Tratamento B'),
            (SELECT id FROM question WHERE name = 'Efectuou tratamento'), 1),
           ((SELECT id FROM activity WHERE name = 'Tratamento B'),
            (SELECT id FROM question WHERE name = 'Tratamento correu bem'), 2),
           ((SELECT id FROM activity WHERE name = 'Tratamento B'),
            (SELECT id FROM question WHERE name = 'Observações'), 3),
            
           ((SELECT id FROM activity WHERE name = 'Tratamento C'),
            (SELECT id FROM question WHERE name = 'Efectuou tratamento'), 1),
           ((SELECT id FROM activity WHERE name = 'Tratamento C'),
            (SELECT id FROM question WHERE name = 'Tratamento correu bem'), 2),
           ((SELECT id FROM activity WHERE name = 'Tratamento C'),
            (SELECT id FROM question WHERE name = 'Observações'), 3),
            
           ((SELECT id FROM activity WHERE name = 'Tratamento D'),
            (SELECT id FROM question WHERE name = 'Efectuou tratamento'), 1),
           ((SELECT id FROM activity WHERE name = 'Tratamento D'),
            (SELECT id FROM question WHERE name = 'Tratamento correu bem'), 2),
           ((SELECT id FROM activity WHERE name = 'Tratamento D'),
            (SELECT id FROM question WHERE name = 'Observações'), 3)
            ;


DO $$

DECLARE var_action1 BIGINT;
DECLARE var BIGINT;

BEGIN
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ACTIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO action (appointmentid, activityid, userid, date)
    VALUES

        --/* Questionário */
           ((SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat1'),
            (SELECT id FROM activity WHERE name = 'Questionário ao paciente na 1ª Consulta'),
            (SELECT id FROM useraccount WHERE username = 'doctor'), CURRENT_TIMESTAMP)
            RETURNING id INTO var_action1;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ANSWER
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Resposta a Questionário ao paciente na 1ª Consulta
INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Nome')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Lídia Monteiro');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Idade')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '50');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Profissão')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Lojista');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Sexo')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Feminino');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Peso')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '70');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Altura')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '1.70');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tamanho do pescoço')) RETURNING id INTO var;
UPDATE answer SET info = 'Não sei o que quer dizer' WHERE id = var;

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Escolaridade')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Liceu');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Carta de condução')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Ligeiros');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Queixa mais importante')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Ando muito cansada durante o dia.');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Horas de sono durante a semana')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '7');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Horas de sono durante o fim-de-semana')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '9');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Trabalha por turnos?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Custa-lhe a adormecer?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Acorda habitualmente durante a noite?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Acorda mais cedo do que queria?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Toma habitualmente comprimidos para dormir?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Bebe regularmente bebidas alcoólicas?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Bebe diariamente café, chá preto ou coca-cola?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Fuma ou já existiu algum período em que fumou?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Já teve algum acidente por ter adormecido a conduzir?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tem hipertensão (tensão alta)?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tem ou já teve problemas do coração?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tem problemas do nariz (entupido, outros)?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Já foi operado ao nariz ou garganta?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Está deprimido?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tem outras doenças?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Historial de doenças')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Diabetes'),
     (var, 'Asma'),
     (var, 'Dislipidémia'),
     (var, 'Arritmia'),
     (var, 'Enfarte');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Medicamentos toma habitualmente')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Uns com nomes complicados, que eu não sei muito bem, para a tensão, para o coração, para as dores de cabeça, para o mau feitio.');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Ressona durante o sono?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Alguém lhe disse que pára de respirar durante o sono?')) RETURNING id INTO var;
UPDATE answer SET info = 'Sou solteira e tenho decência. Não durmo com ninguém!!!' WHERE id = var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Desperta durante a noite com sensação de asfixia?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tem dores de cabeça quando se levanta de manhã?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Quando se levanta sente que não descansou à noite?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tem problemas de concentração ou pouca agilidade mental?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tem sonolência durante o dia?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Quantas vezes se levanta durante a noite para urinar?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '2');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Quando adormece tem sonhos ou pesadelos que parecem reais?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Alguma vez teve sonhos ou pesadelos acordado?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Sem forças durante emoção')) RETURNING id INTO var;
UPDATE answer SET info = 'Não tenho emoções fortes' WHERE id = var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Paralizado a dormir')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Mexe muito as pernas enquanto adormece ou a meio da noite?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Se tem sonolência nota que esta melhora depois de uma sesta?')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Sentado e a ler')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Moderada');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'A ver televisão')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Forte');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Sentado em público')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Moderada');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Como passageiro em viagens')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Ligeira');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Deitado a meio da tarde')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Forte');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Sentado a falar com alguém')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Ligeira');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Sentado após as refeições')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Forte');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'A conduzir')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Nenhuma');

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   APPOINTMENT
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO appointment (eventid, parentid, userid, date)
    VALUES
       -- /* 1a consulta */
           ((SELECT id FROM event                     WHERE name = '1ª Consulta'),
            (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat1'),
            (SELECT id FROM useraccount               WHERE username = 'doctor'), CURRENT_TIMESTAMP);

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ACTIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO action (appointmentid, activityid, userid, date)
    VALUES
      --  /* 1a consulta */
           ((SELECT id FROM appointment WHERE parentid = (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat1')
                         AND eventid = (SELECT id FROM event WHERE name = '1ª Consulta')),
            (SELECT id FROM activity    WHERE name = 'Registo da 1ª Consulta de Patologia do Sono'),
            (SELECT id FROM useraccount WHERE username = 'doctor'), CURRENT_TIMESTAMP)
            RETURNING id INTO var_action1;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ANSWER
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Questionário primeira consulta do médico
INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'IMC')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '30');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'IMC observações')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'A paciente é levemente obesa e apresenta alguns ruídos respiratórios.');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Palato')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Duvidoso');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Orofaringe')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Anormal');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Mallampati')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Classe 3');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Articulação temporo-mandibular')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Laxa');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Posição do mento')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Micrognatia');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Manobra inspiratória nasal')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Anormal');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Sonolência diurna')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Passiva');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Patologia cardio e/ou cerebrovascular')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Impressão clínica')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Roncopatia simples'),
     (var, 'Narcolepsia'),
     (var, 'Má higiene do sono'),
     (var, 'SAOS');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Data da polissonografia com EEG')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '2016-04-12');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Data do teste manutenção da vigília')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '2016-05-25');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Observações')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'A paciente vai voltar em data a marcar consoante a disponibilidade de dispensa da loja.');

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   APPOINTMENT
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO appointment (eventid, parentid, userid, date)
    VALUES
      --  /* Análises e resultados */
           ((SELECT id FROM event                     WHERE name = 'Resultados'),
            (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat1'),
            (SELECT id FROM useraccount               WHERE username = 'doctor'), CURRENT_TIMESTAMP);

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ACTIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO action (appointmentid, activityid, userid, date)
    VALUES

    --    /* Análises e resultados */
           ((SELECT id FROM appointment WHERE parentid = (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat1')
                         AND eventid = (SELECT id FROM event WHERE name = 'Resultados')),
            (SELECT id FROM activity    WHERE name = 'Recolha de Dados Biométricos'),
            (SELECT id FROM useraccount WHERE username = 'labtech'), CURRENT_TIMESTAMP)
            RETURNING id INTO var_action1;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ANSWER
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Dados biométricos
INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Peso')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '80');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Altura')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '1,70');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Ritmo cardíaco')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '90');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tamanho do pescoço')) RETURNING id INTO var;
UPDATE answer SET info = 'A paciente recusou deixar medir o tamanho do pescoço' WHERE id = var;

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Pressão arterial sistólica')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '140');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Pressão arterial diastólica')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '90');

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ACTIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO action (appointmentid, activityid, userid, date)
    VALUES

    --    /* Análises e resultados */
           ((SELECT id FROM appointment WHERE parentid = (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat1')
                         AND eventid = (SELECT id FROM event WHERE name = 'Resultados')),
            (SELECT id FROM activity    WHERE name = 'Recolha de Dados Biométricos'),
            (SELECT id FROM useraccount WHERE username = 'labtech'), CURRENT_TIMESTAMP)
            RETURNING id INTO var_action1;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ANSWER
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Dados biométricos
INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Peso')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '85');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Altura')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '1,69');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Ritmo cardíaco')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '92');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tamanho do pescoço')) RETURNING id INTO var;
UPDATE answer SET info = 'A paciente continua a recusar deixar medir o tamanho do pescoço' WHERE id = var;

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Pressão arterial sistólica')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '150');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Pressão arterial diastólica')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, '95');











-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   APPOINTMENT
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO appointment (eventid, parentid, userid, date)
    VALUES
      --  /* Análises e resultados */
           ((SELECT id FROM event                     WHERE name = 'Fase 1'),
            (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat2'),
            (SELECT id FROM useraccount               WHERE username = 'doctor2'), CURRENT_TIMESTAMP);

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ACTIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO action (appointmentid, activityid, userid, date)
    VALUES

    --    /* Análises e resultados */
           ((SELECT id FROM appointment WHERE parentid = (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat2')
                         AND eventid = (SELECT id FROM event WHERE name = 'Fase 1')),
            (SELECT id FROM activity    WHERE name = 'Tratamento A'),
            (SELECT id FROM useraccount WHERE username = 'doctor2'), CURRENT_TIMESTAMP)
            RETURNING id INTO var_action1;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ANSWER
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Dados biométricos
INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Efectuou tratamento')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tratamento correu bem')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Observações')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Nada a acrescentar');



-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ACTIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO action (appointmentid, activityid, userid, date)
    VALUES

    --    /* Análises e resultados */
           ((SELECT id FROM appointment WHERE parentid = (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat2')
                         AND eventid = (SELECT id FROM event WHERE name = 'Fase 1')),
            (SELECT id FROM activity    WHERE name = 'Tratamento B'),
            (SELECT id FROM useraccount WHERE username = 'doctor2'), CURRENT_TIMESTAMP)
            RETURNING id INTO var_action1;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ANSWER
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Dados biométricos
INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Efectuou tratamento')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tratamento correu bem')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Observações')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Nada a acrescentar');





-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   APPOINTMENT
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO appointment (eventid, parentid, userid, date)
    VALUES
      --  /* Análises e resultados */
           ((SELECT id FROM event                     WHERE name = 'Fase 2'),
            (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat2'),
            (SELECT id FROM useraccount               WHERE username = 'doctor2'), CURRENT_TIMESTAMP);

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ACTIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO action (appointmentid, activityid, userid, date)
    VALUES

    --    /* Análises e resultados */
           ((SELECT id FROM appointment WHERE parentid = (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat2')
                         AND eventid = (SELECT id FROM event WHERE name = 'Fase 2')),
            (SELECT id FROM activity    WHERE name = 'Tratamento C'),
            (SELECT id FROM useraccount WHERE username = 'doctor2'), CURRENT_TIMESTAMP)
            RETURNING id INTO var_action1;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ANSWER
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Dados biométricos
INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Efectuou tratamento')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tratamento correu bem')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Observações')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Muitas dores');



-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ACTIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

INSERT INTO action (appointmentid, activityid, userid, date)
    VALUES

    --    /* Análises e resultados */
           ((SELECT id FROM appointment WHERE parentid = (SELECT id FROM defaultpatientappointment WHERE patientidentifier = 'pat2')
                         AND eventid = (SELECT id FROM event WHERE name = 'Fase 2')),
            (SELECT id FROM activity    WHERE name = 'Tratamento D'),
            (SELECT id FROM useraccount WHERE username = 'doctor2'), CURRENT_TIMESTAMP)
            RETURNING id INTO var_action1;


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                                   ANSWER
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Dados biométricos
INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Efectuou tratamento')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Sim');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Tratamento correu bem')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'Não');

INSERT INTO answer (actionid, questionid)
    VALUES (var_action1, (SELECT id FROM question WHERE name = 'Observações')) RETURNING id INTO var;
INSERT INTO answeritem (answerid, val)
    VALUES (var, 'O paciente desmaiou durante o tratamento');




INSERT INTO permissions (roleid, module, mode, objid)
    VALUES
-- Permissions for event type Consultas
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_eventtype',     'read', (SELECT id FROM eventtype    WHERE name = 'Consulta')),
           ((SELECT id FROM role WHERE name = 'labtech'),   'oi_eventtype',     'read', (SELECT id FROM eventtype    WHERE name = 'Consulta')),
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_eventtype',     'add',  (SELECT id FROM eventtype    WHERE name = 'Consulta')),
           --((SELECT id FROM role WHERE name = 'labtech'),   'oi_eventtype',     'add',  (SELECT id FROM eventtype    WHERE name = 'Consulta')),

-- Permissions for event type Análises e Exames
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_eventtype',     'read', (SELECT id FROM eventtype    WHERE name = 'Declaração do laboratório')),
           ((SELECT id FROM role WHERE name = 'labtech'),   'oi_eventtype',     'read', (SELECT id FROM eventtype    WHERE name = 'Declaração do laboratório')),
           --((SELECT id FROM role WHERE name = 'doctor'),    'oi_eventtype',     'add',  (SELECT id FROM eventtype    WHERE name = 'Declaração do laboratório')),
           ((SELECT id FROM role WHERE name = 'labtech'),   'oi_eventtype',     'add',  (SELECT id FROM eventtype    WHERE name = 'Declaração do laboratório')),

-- Permissions for event type Fase de tratamento
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_eventtype',     'read', (SELECT id FROM eventtype    WHERE name = 'Fase de tratamento')),
           ((SELECT id FROM role WHERE name = 'labtech'),   'oi_eventtype',     'read', (SELECT id FROM eventtype    WHERE name = 'Fase de tratamento')),
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_eventtype',     'add',  (SELECT id FROM eventtype    WHERE name = 'Fase de tratamento')),
           --((SELECT id FROM role WHERE name = 'labtech'),   'oi_eventtype',     'add',  (SELECT id FROM eventtype    WHERE name = 'Fase de tratamento')),

-- Permissions for activity type Questionário
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_activitytype',     'read', (SELECT id FROM activitytype    WHERE name = 'Questionário')),
           ((SELECT id FROM role WHERE name = 'labtech'),   'oi_activitytype',     'read', (SELECT id FROM activitytype    WHERE name = 'Questionário')),
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_activitytype',     'add',  (SELECT id FROM activitytype    WHERE name = 'Questionário')),
           --((SELECT id FROM role WHERE name = 'labtech'),   'oi_activitytype',     'add',  (SELECT id FROM activitytype    WHERE name = 'Questionário')),
-- Permissions for activity type Tratamentos
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_activitytype',     'read', (SELECT id FROM activitytype    WHERE name = 'Tratamentos')),
           ((SELECT id FROM role WHERE name = 'labtech'),   'oi_activitytype',     'read', (SELECT id FROM activitytype    WHERE name = 'Tratamentos')),
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_activitytype',     'add',  (SELECT id FROM activitytype    WHERE name = 'Tratamentos')),
           --((SELECT id FROM role WHERE name = 'labtech'),   'oi_activitytype',     'add',  (SELECT id FROM activitytype    WHERE name = 'Tratamentos')),

-- Permissions for activity type Pedidos de exames
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_activitytype',     'read', (SELECT id FROM activitytype    WHERE name = 'Pedidos de exames')),
           ((SELECT id FROM role WHERE name = 'labtech'),   'oi_activitytype',     'read', (SELECT id FROM activitytype    WHERE name = 'Pedidos de exames')),
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_activitytype',     'add',  (SELECT id FROM activitytype    WHERE name = 'Pedidos de exames')),
           --((SELECT id FROM role WHERE name = 'labtech'),   'oi_activitytype',     'add',  (SELECT id FROM activitytype    WHERE name = 'Pedidos de exames')),

-- Permissions for activity type Resultados de exames
           ((SELECT id FROM role WHERE name = 'doctor'),    'oi_activitytype',     'read', (SELECT id FROM activitytype    WHERE name = 'Resultados de exames')),
           ((SELECT id FROM role WHERE name = 'labtech'),   'oi_activitytype',     'read', (SELECT id FROM activitytype    WHERE name = 'Resultados de exames')),
           --((SELECT id FROM role WHERE name = 'doctor'),    'oi_activitytype',     'add',  (SELECT id FROM activitytype    WHERE name = 'Resultados de exames')),
           ((SELECT id FROM role WHERE name = 'labtech'),   'oi_activitytype',     'add',  (SELECT id FROM activitytype    WHERE name = 'Resultados de exames'));


END $$;

COMMIT;
