DROP TRIGGER IF EXISTS "bu_reset_person_request_pending_from_documents" ON "public"."persons_documents";
DROP FUNCTION IF EXISTS "public"."tr_reset_person_request_pending_from_documents"();

CREATE FUNCTION "public"."tr_reset_person_request_pending_from_documents"()
RETURNS TRIGGER
AS
$BODY$
DECLARE
    v_observed_count NUMERIC DEFAULT 0;
BEGIN

    -- SI EL
    IF NEW.status = 'pending' AND OLD.status = 'observed' THEN
        v_observed_count := (
            SELECT count(id)
            FROM persons_documents
            WHERE persons_requests_id = NEW.persons_requests_id
            AND deleted_at IS NULL
            AND status = 'observed'
            AND id <> NEW.id
        ) + (
            SELECT count(cr.id)
            FROM children_requests AS cr
            INNER JOIN children AS c ON c.id = cr.children_id
            WHERE cr.persons_requests_id = NEW.persons_requests_id
            AND cr.deleted_at IS NULL
            AND c.deleted_at IS NULL
            AND cr.status = 'observed'
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

CREATE TRIGGER "bu_reset_person_request_pending_from_documents"
BEFORE UPDATE OF "status" ON "public"."persons_documents"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tr_reset_person_request_pending_from_documents"();

COMMENT ON TRIGGER bu_reset_person_request_pending_from_documents
ON persons_documents
IS 'Cuando un documento de la solicitud del trabajador cambia su estado de observado a pendiente,
    se debe cambiar el estado de la solicitud a pendiente, siempre y cuando no existan
    otros documentos observados u otras solicitudes de hijos observados.';
