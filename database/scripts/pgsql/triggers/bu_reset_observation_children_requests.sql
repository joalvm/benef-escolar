DROP TRIGGER IF EXISTS "bu_reset_observation_children_requests" ON "public"."children_documents";
DROP FUNCTION IF EXISTS "public"."tr_reset_observation_children_requests"();

CREATE FUNCTION "public"."tr_reset_observation_children_requests"()
RETURNS TRIGGER
AS
$BODY$
DECLARE
    v_observed_count NUMERIC DEFAULT 0;
BEGIN

    IF NEW.status = 'pending' AND OLD.status = 'observed' THEN
        v_observed_count := (
            SELECT count(cd.id)
            FROM children_documents AS cd
            INNER JOIN children_requests AS cr ON cr.id = cd.children_requests_id
            INNER JOIN children AS c ON c.id = cr.children_id
            WHERE cd.children_requests_id = NEW.children_requests_id
            AND cd.deleted_at IS NULL
            AND cr.deleted_at IS NULL
            AND c.deleted_at IS NULL
            AND cd.status = 'observed'
            AND cd.id <> NEW.id
        );

        IF v_observed_count = 0 THEN
            UPDATE children_requests
            SET status = 'pending', updated_at = CURRENT_TIMESTAMP
            WHERE id = NEW.children_requests_id;
        END IF;
    END IF;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER "bu_reset_observation_children_requests"
BEFORE UPDATE OF "status" ON "public"."children_documents"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tr_reset_observation_children_requests"();

COMMENT ON TRIGGER bu_reset_observation_children_requests
ON children_documents
IS 'Cuando un documento observado, de una solicitud de hijo, cambia su estado a
    pendiente, se debe cambiar el estado de la solicitud del hijo a pendiente siempre
    y cuando no tenga otros documentos observados';
