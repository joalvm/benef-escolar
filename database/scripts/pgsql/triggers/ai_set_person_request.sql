DROP TRIGGER IF EXISTS "ai_set_person_request" ON "public"."persons_requests";
DROP FUNCTION IF EXISTS "public"."tr_set_person_request"();

CREATE FUNCTION "public"."tr_set_person_request"()
RETURNS TRIGGER
AS
$BODY$
DECLARE
    v_parent_id int4;
BEGIN

    UPDATE children_requests AS cr
    SET
        persons_requests_id = NEW.id,
        updated_at = current_timestamp
    WHERE
        cr.periods_id = NEW.periods_id
    AND EXISTS (
        SELECT 1 FROM children AS c
        WHERE c.id = cr.children_id
        AND c.persons_id = NEW.persons_id
    );

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER "ai_set_person_request"
AFTER INSERT ON "public"."persons_requests"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tr_set_person_request"();

COMMENT ON TRIGGER ai_set_person_request
ON persons_requests
IS 'Cuando se registra una solicitud de trabajador se debe setear el id de
    la solicitud a cada solicitud de hijo tomando en cuenta el periodo';
