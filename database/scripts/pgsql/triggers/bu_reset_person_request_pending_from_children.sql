DROP TRIGGER IF EXISTS "bu_reset_person_request_pending_from_children" ON "public"."children_requests";
DROP FUNCTION IF EXISTS "public"."tr_reset_person_request_pending_from_children"();

CREATE FUNCTION "public"."tr_reset_person_request_pending_from_children"()
RETURNS TRIGGER
AS
$BODY$
DECLARE
    v_observed_count NUMERIC DEFAULT 0;
BEGIN

    IF NEW.status = 'pending' AND OLD.status = 'observed' THEN
        v_observed_count := (
            SELECT count(id)
            FROM persons_documents
            WHERE persons_requests_id = NEW.persons_requests_id
            AND deleted_at IS NULL
            AND status = 'observed'
        ) + (
            SELECT count(cr.id)
            FROM children_requests AS cr
            INNER JOIN children AS c ON c.id = cr.children_id
            WHERE cr.persons_requests_id = NEW.persons_requests_id
            AND cr.deleted_at IS NULL
            and c.deleted_at IS NULL
            AND cr.status = 'observed'
            AND cr.id <> NEW.id
        );

        IF v_observed_count = 0 THEN
            UPDATE persons_requests
            SET status = 'pending', updated_at = CURRENT_TIMESTAMP
            WHERE id = NEW.persons_requests_id;
        END IF;
    END IF;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER "bu_reset_person_request_pending_from_children"
BEFORE UPDATE OF "status" ON "public"."children_requests"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tr_reset_person_request_pending_from_children"();

COMMENT ON TRIGGER bu_reset_person_request_pending_from_children
ON children_requests
IS 'Cuando una solicitud de hijo pasa de estado observado a pendiente, se debe
    cambiar a pendiente el estado de la solicitud general, siempre y cuando no
    existan otras solicitudes de hijos observadas u otros documentos de solicitud observados';
