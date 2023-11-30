DROP FUNCTION IF EXISTS "public"."fn_has_superior_child"(int4);

CREATE FUNCTION "public"."fn_has_superior_child"(p_persons_id int4)
RETURNS bool
AS
$FUNCTION$
BEGIN
    -- ESTE CODIGO DEBE SER CAMBIADO, EL PROBLEMA ES QUE IDENTIFICA DE MANERA ESTATICA
    -- AL NIVEL EDUCATIVO CON EL ID 5, ESTO DEBE SER DINAMICO Y CONFIGURABLE
    RETURN EXISTS (
        SELECT
            1
        FROM children_requests AS cr
        INNER JOIN children AS c ON c.id = cr.children_id
        WHERE c.deleted_at IS NULL
        AND cr.deleted_at IS NULL
        AND c.persons_id = p_persons_id
        AND cr.education_levels_id = 5
    );
END;
$FUNCTION$
LANGUAGE plpgsql;

SELECT "public"."fn_has_superior_child"(700190);
