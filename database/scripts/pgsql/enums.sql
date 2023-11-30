DROP TYPE IF EXISTS "public"."gender";

CREATE TYPE "public"."gender" AS ENUM (
    'masculino',
    'femenino'
);

DROP TYPE IF EXISTS "public"."person_status";

CREATE TYPE "public"."person_status" AS ENUM (
    'pending',
    'registered',
    'verified'
);

DROP TYPE IF EXISTS "public"."role_types";

CREATE TYPE "public"."role_types" AS ENUM (
    'super_admin',
    'admin',
    'user'
);

DROP TYPE IF EXISTS "public"."request_status";

CREATE TYPE "public"."request_status" AS ENUM (
    'pending',
    'observed',
    'approved',
    'rejected',
    'closed'
);

DROP TYPE IF EXISTS "public"."document_type";

CREATE TYPE "public"."document_type" AS ENUM (
    'dni',
    'studies',
    'bonds'
);

DROP TYPE IF EXISTS "public"."delivery_type";

CREATE TYPE "public"."delivery_type" AS ENUM (
    'pick_in_plant',
    'delivery'
);
