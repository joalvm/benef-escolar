DROP TRIGGER IF EXISTS "ai_reload_persons_request_from_document" ON "public"."persons_documents";
DROP FUNCTION IF EXISTS "public"."tr_reload_persons_request_from_document"();

CREATE FUNCTION "public"."tr_reload_persons_request_from_document"()
RETURNS TRIGGER
AS
$BODY$
BEGIN

    UPDATE persons_requests
    SET
        status = 'pending',
        approved_by = NULL,
        approved_at = NULL,
        updated_at = current_timestamp
    WHERE
        status = 'approved'
    AND id = NEW.persons_requests_id;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER "ai_reload_persons_request_from_document"
AFTER INSERT ON "public"."persons_documents"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tr_reload_persons_request_from_document"();

COMMENT ON TRIGGER ai_reload_persons_request_from_document
ON persons_documents
IS 'Cuando se registra un nuevo documento de solicitud, despues que la
    solicitud está aprobada, se cambia el estado de la solicitud a pendiente';
