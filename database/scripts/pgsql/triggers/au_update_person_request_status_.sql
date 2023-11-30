DROP TRIGGER IF EXISTS "au_update_person_request_status_from_children" ON "public"."children_requests";
DROP TRIGGER IF EXISTS "au_update_person_request_status_from_documents" ON "public"."persons_documents";
DROP FUNCTION IF EXISTS "public"."tr_update_person_request_status"();

CREATE FUNCTION "public"."tr_update_person_request_status"()
RETURNS TRIGGER
AS
$BODY$
DECLARE
    v_approved_count NUMERIC DEFAULT 0;
BEGIN

    IF NEW.status = 'observed' THEN
        UPDATE persons_requests SET status = 'observed' WHERE id = NEW.persons_requests_id;
    ELSEIF NEW.status = 'approved' THEN
        v_approved_count := (
            SELECT count(pd.id)
            FROM persons_documents AS pd
            WHERE pd.persons_requests_id = NEW.persons_requests_id
            AND pd.deleted_at IS NULL
            AND (pd.status = 'observed' OR pd.status = 'pending')
        ) + (
            SELECT count(cr.id)
            FROM children_requests AS cr
            INNER JOIN children AS c ON c.id = cr.children_id
            WHERE cr.persons_requests_id = NEW.persons_requests_id
            AND cr.deleted_at IS NULL
            AND c.deleted_at IS NULL
            AND (cr.status = 'observed' OR cr.status = 'pending')
        );

        IF v_approved_count = 0 THEN
            UPDATE persons_requests
            SET
                status = 'approved',
                approved_at = NEW.approved_at,
                approved_by = NEW.approved_by,
                updated_at = NEW.updated_at
            WHERE id = NEW.persons_requests_id;
        END IF;
    END IF;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER "au_update_person_request_status_from_children"
AFTER UPDATE OF "status" ON "public"."children_requests"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tr_update_person_request_status"();

CREATE TRIGGER "au_update_person_request_status_from_documents"
AFTER UPDATE OF "status" ON "public"."persons_documents"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tr_update_person_request_status"();

COMMENT ON TRIGGER au_update_person_request_status_from_children
ON children_requests
IS 'Cuando una solicitud de hijo y/o un documento de solicitud del trabajador cambian
    su estado a aprobado, se verifica que todos los documentos de solicitud y todos las
    solicitudes de los hijos esten aprobados, para que la solicitud en general cambie su
    estado a aprobado, de lo contrario basta con que exista un observado para que toda la
    solicitud general pase a observado';

COMMENT ON TRIGGER au_update_person_request_status_from_documents
ON persons_documents
IS 'Cuando una solicitud de hijo y/o un documento de solicitud del trabajador cambian
    su estado a aprobado, se verifica que todos los documentos de solicitud y todos las
    solicitudes de los hijos esten aprobados, para que la solicitud en general cambie su
    estado a aprobado, de lo contrario basta con que exista un observado para que toda la
    solicitud general pase a observado';
