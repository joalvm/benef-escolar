DROP TRIGGER IF EXISTS "bi_set_person_request_from_children_request" ON "public"."children_requests";
DROP FUNCTION IF EXISTS "public"."tr_set_person_request_from_children_request"();

CREATE FUNCTION "public"."tr_set_person_request_from_children_request"()
RETURNS TRIGGER
AS
$BODY$
DECLARE
    v_parent_id int4;
BEGIN

    SELECT
        c.persons_id INTO v_parent_id
    FROM children AS c
    WHERE c.id = NEW.children_id;

    NEW.persons_requests_id := (
        SELECT pr.id FROM persons_requests AS pr
        WHERE pr.persons_id = v_parent_id
        AND pr.periods_id = NEW.periods_id
    );

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER "bi_set_person_request_from_children_request"
BEFORE INSERT ON "public"."children_requests"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tr_set_person_request_from_children_request"();

COMMENT ON TRIGGER bi_set_person_request_from_children_request
ON children_requests
IS 'Cuando se registra una nueva solicitud de hijo, se debe consultar si el padre
    tiene una solicitud activa, para poder setear el id de la solicitud del padre
    a la solicitud del hijo';
