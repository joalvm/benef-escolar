DROP FUNCTION IF EXISTS "public"."fn_get_active_period"();

CREATE FUNCTION "public"."fn_get_active_period"()
RETURNS numeric
AS
$FUNCTION$
BEGIN
	RETURN (
        SELECT id
        FROM "public"."periods"
        WHERE active IS TRUE
        ORDER BY created_at DESC
        LIMIT 1
    );
END;
$FUNCTION$
LANGUAGE plpgsql;
