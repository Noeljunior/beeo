-- BEEO SCHEMA
DROP TABLE IF EXISTS useraccount  CASCADE;
DROP TABLE IF EXISTS role         CASCADE;
DROP TABLE IF EXISTS userroles    CASCADE;

CREATE TABLE useraccount (
	id	            BIGSERIAL,
	username        TEXT           UNIQUE  NOT NULL,
	password        TEXT                   NOT NULL,
	name	        TEXT                   NOT NULL,
	email	        TEXT,
	phone	        TEXT,
	mobile	        TEXT,
	url	            TEXT,
	info	        TEXT,
    changepass      BOOLEAN,

	PRIMARY KEY(id)
);

CREATE TABLE role (
	id		        BIGSERIAL,
	name	        TEXT            UNIQUE  NOT NULL,
	description     TEXT,
    letter          TEXT            UNIQUE  NOT NULL,

	PRIMARY KEY(id)
);

CREATE TABLE userroles (
	id	            BIGSERIAL,
	userid          BIGINT                  NOT NULL,
	roleid          BIGINT                  NOT NULL,

	PRIMARY KEY(id)
);

-- ALTER FOREIGN KEYS
ALTER TABLE userroles ADD FOREIGN KEY (userid) REFERENCES useraccount(id) ON DELETE CASCADE;
ALTER TABLE userroles ADD FOREIGN KEY (roleid) REFERENCES role(id)        ON DELETE CASCADE;


-- -- -- -- -- -- -- -- -- -- -- --
--          USER LOGGING
-- -- -- -- -- -- -- -- -- -- -- --
DROP TABLE IF EXISTS      userlog CASCADE;

CREATE TABLE userlog (
    id	            BIGSERIAL,
    date            TIMESTAMP with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,   -- CURRENT_TIMESTAMP
    userid          BIGINT,
    roleid          BIGINT,
    type            TEXT,
    who             TEXT,
    description     TEXT,

	PRIMARY KEY(id)
);

-- ALTER FOREIGN KEYS
ALTER TABLE userlog ADD FOREIGN KEY (userid) REFERENCES useraccount(id) ON DELETE SET NULL;
ALTER TABLE userlog ADD FOREIGN KEY (roleid) REFERENCES role(id)        ON DELETE SET NULL;


-- -- -- -- -- -- -- -- -- -- -- --
--          ORGANIZATIONS
-- -- -- -- -- -- -- -- -- -- -- --
DROP TABLE IF EXISTS organization         CASCADE;
DROP TABLE IF EXISTS userorganization     CASCADE;

CREATE TABLE    organization (
    id                  BIGSERIAL,
    name                TEXT        UNIQUE  NOT NULL,
    address             TEXT,
    postcode            TEXT,
    location            TEXT,
    email               TEXT,
    phone               TEXT,
    fax                 TEXT,
    url                 TEXT,
    info                TEXT,

    PRIMARY KEY(id)
);

CREATE TABLE    userorganization (
    id                  BIGSERIAL,
    userid              BIGINT              NOT NULL,
    organizationid      BIGINT              NOT NULL,
    function            TEXT,
    email               TEXT,
    phone               TEXT,
    mobile              TEXT,
    info                TEXT,

    PRIMARY KEY(id)
);

-- ALTER FOREIGN KEYS
ALTER TABLE userorganization ADD FOREIGN KEY (userid)         REFERENCES useraccount(id)  ON DELETE CASCADE;
ALTER TABLE userorganization ADD FOREIGN KEY (organizationid) REFERENCES organization(id) ON DELETE CASCADE;


-- -- -- -- -- -- -- -- -- -- -- --
-- EVENT TYPES & ACTIVITIES & DATA TYPES
-- -- -- -- -- -- -- -- -- -- -- --
DROP TABLE IF EXISTS event               CASCADE;
DROP TABLE IF EXISTS eventtype           CASCADE;
DROP TABLE IF EXISTS activity            CASCADE;
DROP TABLE IF EXISTS activitytype        CASCADE;
DROP TABLE IF EXISTS question            CASCADE;
DROP TABLE IF EXISTS activityquestion    CASCADE;
DROP TABLE IF EXISTS inputtype           CASCADE;
DROP TABLE IF EXISTS inputitem           CASCADE;
DROP TABLE IF EXISTS inputtypeinputitem  CASCADE;

CREATE TABLE    event (
    id                  BIGSERIAL,
    eventtypeid         BIGINT              NOT NULL,
    name                TEXT        UNIQUE  NOT NULL,
    description         TEXT,
    options             TEXT,

    PRIMARY KEY(id)
);

CREATE TABLE    eventtype (
    id                  BIGSERIAL,
    name                TEXT        UNIQUE  NOT NULL,
    description         TEXT,

    PRIMARY KEY(id)
);

CREATE TABLE    activity (
    id                  BIGSERIAL,
    activitytypeid      BIGINT              NOT NULL,
    name                TEXT        UNIQUE  NOT NULL,
    description         TEXT,
    state               TEXT,
    options             TEXT,

    PRIMARY KEY(id)
);

CREATE TABLE    activitytype (
    id                  BIGSERIAL,
    name                TEXT        UNIQUE  NOT NULL,
    description         TEXT,

    PRIMARY KEY(id)
);

CREATE TABLE    question (
    id                  BIGSERIAL,
    inputtypeid         BIGINT              NOT NULL,
    name                TEXT        UNIQUE  NOT NULL,
    description         TEXT,
    question            TEXT,
    options             TEXT,

    PRIMARY KEY(id)
);

CREATE TABLE    activityquestion (
    id                  BIGSERIAL,
    activityid          BIGINT              NOT NULL,
    questionid          BIGINT              NOT NULL,
    precedence          INT,

    UNIQUE              (activityid, questionid),
    PRIMARY KEY(id)
);

CREATE TABLE    inputtype (
    id                  BIGSERIAL,
    name                TEXT        UNIQUE  NOT NULL,
    description         TEXT,
    options             TEXT,

    PRIMARY KEY(id)
);

CREATE TABLE    inputitem (
    id                  BIGSERIAL,
    name                TEXT        UNIQUE  NOT NULL,
    description         TEXT,

    PRIMARY KEY(id)
);

CREATE TABLE    inputtypeinputitem (
    id                  BIGSERIAL,
    inputtypeid         BIGINT,
    inputitemid         BIGINT,
    precedence          INT,

    PRIMARY KEY(id)
);

-- ALTER FOREIGN KEYS
ALTER TABLE event                   ADD FOREIGN KEY (eventtypeid)    REFERENCES eventtype(id);
ALTER TABLE activity                ADD FOREIGN KEY (activitytypeid) REFERENCES activitytype(id);
ALTER TABLE question                ADD FOREIGN KEY (inputtypeid)    REFERENCES inputtype(id);
ALTER TABLE activityquestion        ADD FOREIGN KEY (activityid)     REFERENCES activity(id)        ON DELETE CASCADE;
ALTER TABLE activityquestion        ADD FOREIGN KEY (questionid)     REFERENCES question(id);
ALTER TABLE inputtypeinputitem      ADD FOREIGN KEY (inputtypeid)    REFERENCES inputtype(id)       ON DELETE CASCADE;
ALTER TABLE inputtypeinputitem      ADD FOREIGN KEY (inputitemid)    REFERENCES inputitem(id);


-- -- -- -- -- -- -- -- -- -- -- --
--          EVENTS & ACTIONS
-- -- -- -- -- -- -- -- -- -- -- --
DROP TABLE IF EXISTS appointment       CASCADE;
DROP TABLE IF EXISTS action           CASCADE;
DROP TABLE IF EXISTS answer           CASCADE;
DROP TABLE IF EXISTS answeritem       CASCADE;

CREATE TABLE    appointment (
    id                  BIGSERIAL,
    eventid             BIGINT              NOT NULL,
    parentid            BIGINT,
    userid              BIGINT              NOT NULL,
    state               TEXT,
    date                TIMESTAMP with time zone DEFAULT now(),
    name                TEXT,

    PRIMARY KEY(id)
);

CREATE TABLE action (
    id                  BIGSERIAL,
    appointmentid       BIGINT              NOT NULL,
    activityid          BIGINT              NOT NULL,
    userid              BIGINT              NOT NULL,
    state               TEXT,
    date                TIMESTAMP with time zone DEFAULT now(),

    PRIMARY KEY(id)
);

CREATE TABLE answer (
    id                  BIGSERIAL,
    actionid            BIGINT              NOT NULL,
    questionid          BIGINT              NOT NULL,
    info                TEXT,

    PRIMARY KEY(id)
);

CREATE TABLE answeritem (
    id                  BIGSERIAL,
    answerid            BIGINT              NOT NULL,
    val                 TEXT,

    PRIMARY KEY(id)
);

-- ALTER FOREIGN KEYS
ALTER TABLE appointment ADD FOREIGN KEY (parentid)      REFERENCES appointment(id);
ALTER TABLE appointment ADD FOREIGN KEY (userid)        REFERENCES useraccount(id);
ALTER TABLE appointment ADD FOREIGN KEY (eventid)       REFERENCES event(id);
ALTER TABLE action      ADD FOREIGN KEY (appointmentid) REFERENCES appointment(id);
ALTER TABLE action      ADD FOREIGN KEY (userid)        REFERENCES useraccount(id);
ALTER TABLE action      ADD FOREIGN KEY (activityid)    REFERENCES activity(id);
ALTER TABLE answer      ADD FOREIGN KEY (actionid)      REFERENCES action(id);
ALTER TABLE answer      ADD FOREIGN KEY (questionid)    REFERENCES question(id);
ALTER TABLE answeritem  ADD FOREIGN KEY (answerid)      REFERENCES answer(id);


-- -- -- -- -- -- -- -- -- -- -- --
--             PATIENTS
-- -- -- -- -- -- -- -- -- -- -- --
DROP TABLE IF EXISTS patient         CASCADE;

CREATE TABLE    patient (
    id                  BIGSERIAL,
    identifier          TEXT        UNIQUE  NOT NULL,
    appointmentid       BIGINT      UNIQUE  NOT NULL,
    userid              BIGINT              NOT NULL,

    PRIMARY KEY(id)
);

ALTER TABLE patient      ADD FOREIGN KEY (appointmentid) REFERENCES appointment(id);
ALTER TABLE patient      ADD FOREIGN KEY (userid)        REFERENCES useraccount(id);


-- -- -- -- -- -- -- -- -- -- -- --
--            PERMISSIONS
-- -- -- -- -- -- -- -- -- -- -- --
DROP TABLE IF EXISTS permissions     CASCADE;

CREATE TABLE    permissions (
    id                  BIGSERIAL,
    roleid              BIGINT              NOT NULL,
    objid               BIGINT,
    module              TEXT,
    mode                TEXT,

    PRIMARY KEY(id)
);

ALTER TABLE permissions ADD FOREIGN KEY (roleid)   REFERENCES role(id) ON DELETE CASCADE;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--      SOME TRIGGER TO MAKE THIS CONSISTENT AND EASIER TO MANAGE
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- -- -- -- -- -- -- -- -- -- -- --
-- AFTER INSERT PATIENT
-- -- -- -- -- -- -- -- -- -- -- --
DROP FUNCTION IF EXISTS tgf_beforeinsertpatient() CASCADE;
DROP TRIGGER IF EXISTS tg_beforeinsertpatient ON patient CASCADE;
CREATE OR REPLACE FUNCTION tgf_beforeinsertpatient() RETURNS TRIGGER AS $$
    DECLARE
        apid BIGINT;
    BEGIN
        INSERT INTO appointment (eventid, userid, date)
            VALUES (0, NEW.userid, CURRENT_TIMESTAMP)
            RETURNING id INTO apid;
        NEW.appointmentid := apid;
        RETURN NEW;
END; $$ LANGUAGE 'plpgsql';
CREATE TRIGGER tg_beforeinsertpatient BEFORE INSERT ON patient
FOR EACH ROW EXECUTE PROCEDURE tgf_beforeinsertpatient();


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                        SOME USEFUL FUNCTIONS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- -- -- -- -- -- -- -- -- -- -- --
--          appointmentpath()
-- -- -- -- -- -- -- -- -- -- -- --
DROP FUNCTION IF EXISTS appointmentpath(apid BIGINT) CASCADE;
CREATE OR REPLACE FUNCTION appointmentpath(apid BIGINT)
    RETURNS TABLE(id BIGINT, depth INT, parentid BIGINT, eventid BIGINT, name TEXT, description TEXT)  AS
$$
    WITH RECURSIVE path AS (
       SELECT id, 0 AS depth, parentid, eventid
           FROM appointment
           WHERE id = apid
       UNION ALL
       SELECT r.id, path.depth + 1 AS depth, r.parentid, r.eventid
           FROM appointment r
               JOIN path on path.parentid = r.id
    )
    SELECT path.*, event.name, event.description FROM path
    JOIN event ON path.eventid = event.id
    ORDER BY path.depth DESC
$$    LANGUAGE sql;

-- -- -- -- -- -- -- -- -- -- -- --
--          getappointmentpatient()
-- -- -- -- -- -- -- -- -- -- -- --
DROP FUNCTION IF EXISTS getappointmentpatient(apid BIGINT) CASCADE;
CREATE OR REPLACE FUNCTION getappointmentpatient(apid BIGINT)
    RETURNS TABLE(id BIGINT, identifier TEXT, appointmentid BIGINT, userid BIGINT)  AS
$$
    SELECT patient.* FROM appointmentpath(apid)
    JOIN patient
      ON appointmentid = appointmentpath.id
    WHERE parentid IS NULL
$$    LANGUAGE sql;

-- -- -- -- -- -- -- -- -- -- -- --
--          getactionanswers()
-- -- -- -- -- -- -- -- -- -- -- --
DROP FUNCTION IF EXISTS getactionanswers(acid BIGINT) CASCADE;
CREATE OR REPLACE FUNCTION getactionanswers(acid BIGINT)
    RETURNS TABLE(qid BIGINT, qquestion TEXT, qoptions TEXT,
                  itid BIGINT, itname TEXT, itoptions TEXT,
                  answeritems TEXT, info TEXT) AS $$
DECLARE
    atid BIGINT;
BEGIN
    SELECT activityid INTO atid FROM action WHERE id = acid;

    RETURN QUERY
        SELECT bar.qid, bar.qquestion, bar.qoptions, bar.itid, bar.itname, bar.itoptions,
               foo.answeritems, foo.info
        FROM (SELECT * FROM activityquestions WHERE aid = atid) AS bar
        LEFT JOIN (SELECT * FROM answeritems WHERE actionid = acid) AS foo
          ON bar.qid = foo.questionid
        ORDER BY qprecedence;
END; $$ LANGUAGE plpgsql;
-- -- -- -- -- -- -- -- -- -- -- --
--          getoldactionanswers()
-- -- -- -- -- -- -- -- -- -- -- --
DROP FUNCTION IF EXISTS getoldactionanswers(acid BIGINT) CASCADE;
CREATE OR REPLACE FUNCTION getoldactionanswers(acid BIGINT)
    RETURNS TABLE(qid BIGINT, qquestion TEXT, qoptions TEXT,
                  itid BIGINT, itname TEXT, itoptions TEXT,
                  answeritems TEXT, info TEXT) AS $$
DECLARE
    atid BIGINT;
BEGIN
    SELECT activityid INTO atid FROM action WHERE id = acid;

    RETURN QUERY
        SELECT foo.questionid AS qid, questionsinfo.question AS qquestion, questionsinfo.options AS qoptions,
               questionsinfo.inputtypeid AS itid, questionsinfo.itname, questionsinfo.itoptions,
               foo.answeritems, foo.info
        FROM (SELECT * FROM activityquestions WHERE aid = 5) AS bar
        RIGHT JOIN (SELECT * FROM answeritems WHERE actionid = 7) AS foo
          ON bar.qid = foo.questionid
        JOIN questionsinfo
          ON foo.questionid = questionsinfo.id
        WHERE bar.qid IS NULL
        ORDER BY qprecedence;
END; $$ LANGUAGE plpgsql;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                    SOME REQUIRED DEFAULT VALUES
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- -- -- -- -- -- -- -- -- -- -- --
-- USERACCOUNTS & ROLES & PERMISSIONS
-- -- -- -- -- -- -- -- -- -- -- --
-- add the sys admin role and user
INSERT INTO role (id, name, description, letter) -- the sysadmin
    VALUES (0, 'sysadmin', 'sysadmin', 'S');
-- add an admin account
INSERT INTO useraccount (id, username, password, name)
    VALUES (0, 'sysadmin', '', 'sysadmin');
-- assign sysadmin a role of sysadmin
INSERT INTO userroles (id, userid, roleid)
    VALUES (0, 0, 0);
-- add sysadmin abitilty to sysadmin the site
INSERT INTO permissions (id, roleid, module, mode)
    VALUES (0, 0, 'uim_sysadmin', 'dv');

-- -- -- -- -- -- -- -- -- -- -- --
--          DATA TYPES
-- -- -- -- -- -- -- -- -- -- -- --
-- add the primitive data types
INSERT INTO inputtype (name, description)
    VALUES ('Number',    'Default number answer type'),
           ('Text',      'Default text answer type'),
           ('Long Text', 'Default long text answer type'),
           ('Date',      'Default date answer type'),
           ('Time',      'Default time answer type'),
           ('Separator', 'Default separator');
SELECT setval('inputtype_id_seq', 99); -- default/system/reserved/primitive items up to 99

INSERT INTO inputitem (name, description)
    VALUES ('Number',    'Default number input'),
           ('Text',      'Default text input'),
           ('Long Text', 'Default long text input'),
           ('Date',      'Default date input'),
           ('Time',      'Default time input'),
           ('Separator', 'Default separator');
SELECT setval('inputitem_id_seq', 99); -- default/system/reserved/primitive items up to 99

INSERT INTO inputtypeinputitem (inputtypeid, inputitemid)
    VALUES ((SELECT id FROM inputtype  WHERE name = 'Number'),
            (SELECT id FROM inputitem  WHERE name = 'Number')),
           ((SELECT id FROM inputtype  WHERE name = 'Text'),
            (SELECT id FROM inputitem  WHERE name = 'Text')),
           ((SELECT id FROM inputtype  WHERE name = 'Long Text'),
            (SELECT id FROM inputitem  WHERE name = 'Long Text')),
           ((SELECT id FROM inputtype  WHERE name = 'Date'),
            (SELECT id FROM inputitem  WHERE name = 'Date')),
           ((SELECT id FROM inputtype  WHERE name = 'Time'),
            (SELECT id FROM inputitem  WHERE name = 'Time')),
           ((SELECT id FROM inputtype  WHERE name = 'Separator'),
            (SELECT id FROM inputitem  WHERE name = 'Separator'));

-- -- -- -- -- -- -- -- -- -- -- --
--       DEFAULT EVENT TYPES
-- -- -- -- -- -- -- -- -- -- -- --
INSERT INTO eventtype (id, name, description)
    VALUES (0, 'Default Patient Event', 'Default Patient Event');
INSERT INTO event (id, eventtypeid, name, description)
    VALUES (0, 0, 'Default Patient Event', 'Default Patient Event');

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--                          SOME USEFUL VIEWS
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- -- -- -- -- -- -- -- -- -- -- --
--          eventtype roles permissions
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS eventtyperolepermissions CASCADE;
CREATE OR REPLACE VIEW eventtyperolepermissions AS
    SELECT id,
        STRING_AGG(foo.readperm, ', ') AS readperm,
        STRING_AGG(foo.addperm, ', ') AS addperm
        FROM (
            SELECT eventtype.id,
            STRING_AGG(permission.roleid::text, ', ' ORDER BY permission.roleid ASC) AS readperm,
            NULL AS addperm
            FROM eventtype
            LEFT JOIN (SELECT * FROM permissions WHERE module LIKE 'oi_eventtype' AND mode = 'read') AS permission
              ON permission.objid = eventtype.id
            GROUP BY eventtype.id, permission.mode
        UNION
            SELECT eventtype.id,
            NULL AS readperm,
            STRING_AGG(permission.roleid::text, ', ' ORDER BY permission.roleid ASC) AS addperm
            FROM eventtype
            LEFT JOIN (SELECT * FROM permissions WHERE module LIKE 'oi_eventtype' AND mode = 'add') AS permission
              ON permission.objid = eventtype.id
            GROUP BY eventtype.id, permission.mode
        ) AS foo
        WHERE id > 0
        GROUP BY id;
-- -- -- -- -- -- -- -- -- -- -- --
--          activitytype roles permissions
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS activitytyperolepermissions CASCADE;
CREATE OR REPLACE VIEW activitytyperolepermissions AS
    SELECT id,
        STRING_AGG(foo.readperm, ', ') AS readperm,
        STRING_AGG(foo.addperm, ', ') AS addperm
        FROM (
            SELECT activitytype.id,
            STRING_AGG(permission.roleid::text, ', ' ORDER BY permission.roleid ASC) AS readperm,
            NULL AS addperm
            FROM activitytype
            LEFT JOIN (SELECT * FROM permissions WHERE module LIKE 'oi_activitytype' AND mode = 'read') AS permission
              ON permission.objid = activitytype.id
            GROUP BY activitytype.id, permission.mode
        UNION
            SELECT activitytype.id,
            NULL AS readperm,
            STRING_AGG(permission.roleid::text, ', ' ORDER BY permission.roleid ASC) AS addperm
            FROM activitytype
            LEFT JOIN (SELECT * FROM permissions WHERE module LIKE 'oi_activitytype' AND mode = 'add') AS permission
              ON permission.objid = activitytype.id
            GROUP BY activitytype.id, permission.mode
        ) AS foo
        WHERE id > 0
        GROUP BY id;

-- -- -- -- -- -- -- -- -- -- -- --
--          userinfo
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS userinfo CASCADE;
CREATE OR REPLACE VIEW userinfo AS
    SELECT useraccount.id, useraccount.name, useraccount.username, useraccount.email, useraccount.phone, useraccount.mobile, useraccount.url, useraccount.info,
        STRING_AGG(role.id::text, ', ' ORDER BY role.letter ASC) AS roles,
        STRING_AGG(role.letter, ', '   ORDER BY role.letter ASC) AS rolesletters
        FROM useraccount
            FULL OUTER JOIN userroles
                ON userroles.userid = useraccount.id
            FULL OUTER JOIN role
                ON role.id = userroles.roleid
         WHERE useraccount.id IS NOT NULL
           AND useraccount.id > 0
         GROUP BY useraccount.id;
-- -- -- -- -- -- -- -- -- -- -- --
--          userlogs
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS userlogs CASCADE;
CREATE OR REPLACE VIEW userlogs AS
    SELECT userlog.userid AS userid, userlog.date, role.name AS rolename, userlog.type, userlog.description
        FROM userlog
        LEFT JOIN role
          ON userlog.roleid = role.id;
-- -- -- -- -- -- -- -- -- -- -- --
--          userorgs
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS userorgs CASCADE;
CREATE OR REPLACE VIEW userorgs AS
    SELECT organization.name AS oname, useraccount.name  AS uname,
           userorganization.*
        FROM userorganization
            JOIN organization
              ON organization.id = userorganization.organizationid
            JOIN useraccount
              ON useraccount.id  = userorganization.userid;
-- -- -- -- -- -- -- -- -- -- -- --
--          ACTIVITY INFO
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS activityinfo CASCADE;
CREATE OR REPLACE VIEW activityinfo AS
    SELECT activity.*, activitytype.name AS atname,
        STRING_AGG(question.id::text, ', ' ORDER BY activityquestion.precedence ASC) AS qids
        FROM activity
            JOIN activitytype
              ON activity.activitytypeid = activitytype.id
            JOIN activityquestion
              ON activityquestion.activityid = activity.id
            JOIN question
              ON activityquestion.questionid = question.id
        GROUP BY activity.id, activitytype.id;
-- -- -- -- -- -- -- -- -- -- -- --
--          QUESTIONS OF ACTIVITIES
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS activityquestions CASCADE;
CREATE OR REPLACE VIEW activityquestions AS
    SELECT activityquestion.activityid AS aid,
        question.id AS qid, question.name AS qname, question.description AS qdescription, question.question AS qquestion, question.options AS qoptions, activityquestion.precedence AS qprecedence,
        inputtype.id AS itid, inputtype.name AS itname, inputtype.description AS itdescription, inputtype.options AS itoptions,
        STRING_AGG(inputitem.name,     ';::;;' ORDER BY inputtypeinputitem.precedence ASC) AS inputs,
        STRING_AGG(inputitem.id::text, ', ' ORDER BY inputtypeinputitem.precedence ASC) AS inputsid
        FROM activityquestion
            JOIN question
              ON activityquestion.questionid = question.id
            JOIN inputtype
              ON question.inputtypeid = inputtype.id
            JOIN inputtypeinputitem
              ON inputtypeinputitem.inputtypeid = inputtype.id
            JOIN inputitem
              ON inputtypeinputitem.inputitemid = inputitem.id
        GROUP BY inputtype.id, question.id, activityquestion.id;
-- -- -- -- -- -- -- -- -- -- -- --
--          QUESTIONS INFO
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS questionsinfo CASCADE;
CREATE OR REPLACE VIEW questionsinfo AS
    SELECT question.*, inputtype.name AS itname, inputtype.description AS itdescription, inputtype.options AS itoptions
        FROM question
        JOIN inputtype
          ON question.inputtypeid = inputtype.id
        ORDER BY question.name;

-- -- -- -- -- -- -- -- -- -- -- --
--          eventinfo
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS eventinfo CASCADE;
CREATE OR REPLACE VIEW eventinfo AS
    SELECT event.*, eventtype.name AS etname, eventtype.description AS etdescription, readperm, addperm
    FROM event
        JOIN eventtype
          ON eventtypeid = eventtype.id
        JOIN eventtyperolepermissions
          ON eventtyperolepermissions.id = eventtype.id
    WHERE event.id > 0;

-- -- -- -- -- -- -- -- -- -- -- --
--          appointmentinfo
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS appointmentinfo CASCADE;
CREATE OR REPLACE VIEW appointmentinfo AS
    SELECT appointment.*, appointment.id AS appointmentid,
        event.name AS ename, eventtype.name AS etname, useraccount.name AS uname
        FROM appointment
        JOIN event ON eventid = event.id
        JOIN eventtype ON event.eventtypeid = eventtype.id
        JOIN useraccount ON useraccount.id = appointment.userid;

-- -- -- -- -- -- -- -- -- -- -- --
--          patientinfo
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS patientinfo CASCADE;
CREATE OR REPLACE VIEW patientinfo AS
    SELECT patient.*, appointment.date, appointment.eventid, appointment.name AS apname,
        event.name AS ename, eventtype.name AS etname,
        useraccount.name AS uname
        FROM patient
        JOIN appointment ON appointment.id = appointmentid
        JOIN event ON appointment.eventid = event.id
        JOIN eventtype ON event.eventtypeid = eventtype.id
        JOIN useraccount ON patient.userid = useraccount.id;

-- -- -- -- -- -- -- -- -- -- -- --
--          defaultpatientappointment
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS defaultpatientappointment CASCADE;
CREATE OR REPLACE VIEW defaultpatientappointment AS
    SELECT appointment.*, patient.id AS patientid, patient.identifier AS patientidentifier
        FROM patient
            JOIN appointment
              ON patient.appointmentid = appointment.id;

-- -- -- -- -- -- -- -- -- -- -- --
--          appointmentchildren
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS appointmentchildren CASCADE;
CREATE OR REPLACE VIEW appointmentchildren AS
    SELECT appointments.*, useraccount.name AS uname, useraccount.id AS uid --, useraccount.username
        FROM (
                SELECT 'appointment' AS type, appointment.id, userid, date, parentid AS parentid,
                    appointment.name AS apname, eventid, event.name AS ename, eventtype.name AS etname,
                    NULL AS activityid, NULL AS aname, NULL AS atname, -- NULLS
                    permission.roleid, STRING_AGG(permission.mode, ', ' ORDER BY permission.mode ASC) AS modes
                    FROM appointment
                    JOIN event
                      ON eventid = event.id
                    JOIN eventtype
                      ON eventtypeid = eventtype.id
                    LEFT JOIN (SELECT * FROM permissions WHERE module LIKE 'oi_eventtype') AS permission
                      ON permission.objid = eventtype.id
                    GROUP BY appointment.id, event.id, eventtype.id, permission.roleid

            UNION

                SELECT 'action' AS type, action.id, userid, date, appointmentid AS parentid,
                    NULL AS apname, NULL AS eventid, NULL AS ename, NULL AS etname, -- NULLS
                    action.activityid, activity.name AS aname, activitytype.name AS atname,
                    permission.roleid, STRING_AGG(permission.mode, ', ' ORDER BY permission.mode ASC) AS modes
                    FROM action
                    JOIN activity
                      ON action.activityid = activity.id
                    JOIN activitytype
                      ON activity.activitytypeid = activitytype.id
                    LEFT JOIN (SELECT * FROM permissions WHERE module LIKE 'oi_activitytype') AS permission
                      ON permission.objid = activitytype.id
                    GROUP BY action.id, activity.id, activitytype.id, permission.roleid
        ) AS appointments
        JOIN useraccount
          ON userid = useraccount.id
        WHERE appointments.id IS NOT NULL;

-- -- -- -- -- -- -- -- -- -- -- --
--          actioninfo
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS actioninfo CASCADE;
CREATE OR REPLACE VIEW actioninfo AS
    SELECT action.*, useraccount.name AS uname, useraccount.id AS uid,
    activity.name AS aname, activitytype.name AS atname
    FROM action
        JOIN useraccount
          ON userid = useraccount.id
        JOIN activity
          ON activityid = activity.id
        JOIN activitytype
          ON activitytypeid = activitytype.id;

-- -- -- -- -- -- -- -- -- -- -- --
--          answeritems
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS answeritems CASCADE;
CREATE OR REPLACE VIEW answeritems AS
    SELECT answer.questionid, answer.actionid, answer.info,
           STRING_AGG(answeritem.val, ', ' ORDER BY answeritem.val ASC) AS answeritems
        FROM answer
            FULL OUTER JOIN answeritem  ON answeritem.answerid = answer.id
            JOIN action                 ON answer.actionid = action.id
            JOIN question               ON answer.questionid = question.id
        GROUP BY answer.id, action.id, question.id;

-- -- -- -- -- -- -- -- -- -- -- --
--          activity/event permissions
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS activityeventpermissions CASCADE;
CREATE OR REPLACE VIEW activityeventpermissions AS
        SELECT 'activity' AS type,
               activityinfo.id AS tid, activityinfo.atname AS tname, activityinfo.name,
               permission.roleid,
               STRING_AGG(permission.mode, ', ' ORDER BY permission.mode ASC) AS modes
            FROM activityinfo
            JOIN (SELECT * FROM permissions WHERE module LIKE 'oi_activitytype') AS permission
              ON permission.objid = activityinfo.activitytypeid
            GROUP BY activityinfo.id, activityinfo.name, activityinfo.atname, permission.roleid
    UNION
        SELECT 'event' AS type,
               eventinfo.id AS tid, eventinfo.etname AS tname, eventinfo.name,
               permission.roleid,
               STRING_AGG(permission.mode, ', ' ORDER BY permission.mode ASC) AS modes
            FROM eventinfo
            JOIN (SELECT * FROM permissions WHERE module LIKE 'oi_eventtype') AS permission
              ON permission.objid = eventinfo.eventtypeid
            GROUP BY eventinfo.id, eventinfo.name, eventinfo.etname, permission.roleid;

-- -- -- -- -- -- -- -- -- -- -- --
--          actionanswers
-- -- -- -- -- -- -- -- -- -- -- --
DROP VIEW IF EXISTS actionanswers CASCADE;
CREATE OR REPLACE VIEW actionanswers AS
    SELECT
        pat.id AS pid, pat.identifier AS pidentifier,
        actioninfo.activityid, actioninfo.aname, actioninfo.date AS adate, actioninfo.uid, actioninfo.uname,
        questionsinfo.id AS qid, questionsinfo.question, questionsinfo.itname,
        answeritems.answeritems, answeritems.info
        FROM answeritems
        JOIN actioninfo
          ON actioninfo.id = answeritems.actionid
        JOIN questionsinfo
          ON questionsinfo.id = answeritems.questionid
        JOIN getappointmentpatient(actioninfo.appointmentid) AS pat
          on pat.appointmentid = actioninfo.appointmentid;


