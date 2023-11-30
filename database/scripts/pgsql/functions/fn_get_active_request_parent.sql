DROP FUNCTION IF EXISTS "public"."fn_get_active_request_parent"(int4);

CREATE FUNCTION "public"."fn_get_active_request_parent"(p_children_id int4)
RETURNS numeric
AS
$FUNCTION$
BEGIN
    RETURN (
        SELECT id
        FROM "public"."persons_requests" AS pr
        WHERE pr.deleted_at IS NULL
        AND pr.periods_id = fn_get_active_period()
        AND EXISTS (
            SELECT 1 FROM public.children AS ch
            WHERE ch.id = p_children_id
            AND ch.persons_id = pr.persons_id
            AND ch.deleted_at IS NULL
        )
        LIMIT 1
    );
END;
$FUNCTION$
LANGUAGE plpgsql;
