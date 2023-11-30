DROP TRIGGER IF EXISTS "ai_reload_persons_request" ON "public"."children_documents";
DROP FUNCTION IF EXISTS "public"."tr_reload_persons_request"();

CREATE FUNCTION "public"."tr_reload_persons_request"()
RETURNS TRIGGER
AS
$BODY$
DECLARE
    v_parent_id int4;
    v_period_id int4;
BEGIN

    SELECT
        cr.periods_id,
        c.persons_id
        INTO
        v_period_id,
        v_parent_id
    FROM children_requests AS cr
    INNER JOIN children AS c ON c.id = cr.children_id
    WHERE c.deleted_at IS NULL
    AND cr.deleted_at IS NULL
    AND cr.id = NEW.children_requests_id;

    UPDATE children_requests
    SET
        status = 'pending',
        approved_at = NULL,
        approved_by = NULL,
        updated_at = current_timestamp
    WHERE
        id = NEW.children_requests_id
    AND status = 'approved';

    UPDATE persons_requests
    SET
        status = 'pending',
        approved_at = NULL,
        approved_by = NULL,
        updated_at = current_timestamp
    WHERE
        status = 'approved'
    AND persons_id = v_parent_id
    AND periods_id = v_period_id;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER "ai_reload_persons_request"
AFTER INSERT ON "public"."children_documents"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tr_reload_persons_request"();

COMMENT ON TRIGGER ai_reload_persons_request
ON children_documents
IS 'Cuando se agrega un nuevo documento de hijo a una solicitud
    aprobada se debe cambiar el estado de la solicitud a pendiente';
