DROP TRIGGER IF EXISTS "au_update_children_requests_status" ON "public"."children_documents";
DROP FUNCTION IF EXISTS "public"."tr_update_children_requests_status"();

CREATE FUNCTION "public"."tr_update_children_requests_status"()
RETURNS TRIGGER
AS
$BODY$
DECLARE
    v_approved_count NUMERIC DEFAULT 0;
BEGIN

    IF NEW.status = 'observed' THEN
        UPDATE children_requests SET status = 'observed' WHERE id = NEW.children_requests_id;
    ELSEIF NEW.status = 'approved' THEN
        v_approved_count := (
            SELECT count(cd.id)
            FROM children_documents AS cd
            INNER JOIN children_requests AS cr ON cr.id = cd.children_requests_id
            INNER JOIN children AS c ON c.id = cr.children_id
            WHERE cd.children_requests_id = NEW.children_requests_id
            AND cd.deleted_at IS NULL
            AND cr.deleted_at IS NULL
            AND c.deleted_at IS NULL
            AND (cd.status = 'observed' OR cd.status = 'pending')
        );

        IF v_approved_count = 0 THEN
            UPDATE children_requests
            SET
                status = 'approved',
                approved_at = NEW.approved_at,
                approved_by = NEW.approved_by,
                updated_at = NEW.updated_at
            WHERE id = NEW.children_requests_id;
        END IF;
    END IF;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER "au_update_children_requests_status"
AFTER UPDATE OF "status" ON "public"."children_documents"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tr_update_children_requests_status"();

COMMENT ON TRIGGER au_update_children_requests_status
ON children_documents
IS 'Cuando todos los documentos de la solicitud de un hijo estan aprobados,
    el estado de la solicitud del niño debe pasar a aprobada, por el contrario
    basta con que exista un documento observado para que toda la solicitud pase
    a observada';
