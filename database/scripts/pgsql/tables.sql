CREATE TABLE "public"."periods" (
    "id" serial4 NOT NULL,
    "name" varchar NOT NULL,
    "start_date" date NOT NULL,
    "finish_date" date NOT NULL,
    "amount_bonds" NUMERIC NOT NULL,
    "max_amount_loan" NUMERIC NOT NULL,
    "max_children" NUMERIC NULL DEFAULT 1,
    "active" bool NULL DEFAULT false,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "public"."departments" (
    "id" serial4 NOT NULL,
    "name" varchar NOT NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "public"."provinces" (
    "id" serial4 NOT NULL,
    "departments_id" int4 NOT NULL,
    "name" varchar NOT NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "public"."districts" (
    "id" serial4 NOT NULL,
    "provinces_id" int4 NOT NULL,
    "name" varchar NOT NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "public"."plants" (
    "id" serial4 NOT NULL,
    "name" varchar NOT NULL,
    "districts_id" int4 NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id"),
    UNIQUE ("name")
);

CREATE TABLE "public"."education_levels" (
    "id" serial4 NOT NULL,
    "name" varchar NOT NULL,
    "amount" NUMERIC NOT NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "public"."units" (
    "id" serial4 NOT NULL,
    "name" varchar NOT NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "public"."boats" (
    "id" serial4 NOT NULL,
    "name" varchar NOT NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "public"."persons" (
    "id" int4 NOT NULL,
    "units_id" int4 NULL,
    "boats_id" int4 NULL,
    "names" varchar NOT NULL,
    "paternal_surname" varchar NULL,
    "maternal_surname" varchar NULL,
    "dni" varchar(8) NULL,
    "gender" "public"."gender" NOT NULL,
    "birth_date" date NOT NULL,
    "hiring_date" date NOT NULL,
    "email" varchar NULL,
    "phone" varchar NULL,
    "status" "public"."person_status" NOT NULL DEFAULT 'pending',
    "responsable" int4[] NULL DEFAULT '{}'::int4[],
    "created_at" timestamptz(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0),
    PRIMARY KEY ("id")
);

CREATE TABLE "public"."persons_requests" (
    "id" serial4 NOT NULL,
    "persons_id" int4 NOT NULL,
    "periods_id" int4 NOT NULL,
    "status" "public"."request_status" NOT NULL DEFAULT 'pending',
    "approved_at" timestamptz(0) NULL,
    "approved_by" int4 NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id"),
    UNIQUE ("persons_id", "periods_id", "deleted_at")
)
;

CREATE TABLE "public"."persons_documents" (
    "id" serial4 NOT NULL,
    "persons_requests_id" int4 NOT NULL,
    "file" varchar NOT NULL,
    "status" "public"."request_status" NOT NULL DEFAULT 'pending',
    "approved_at" timestamptz(0) NULL,
    "approved_by" int4 NULL,
    "observation" text NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
)
;

CREATE TABLE "public"."children" (
    "id" serial4 NOT NULL,
    "persons_id" int4 NOT NULL,
    "name" varchar NOT NULL,
    "paternal_surname" varchar NOT NULL,
    "maternal_surname" varchar NOT NULL,
    "gender" "public"."gender" NOT NULL,
    "birth_date" date NOT NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
)
;

CREATE TABLE "public"."children_requests" (
    "id" serial4 NOT NULL,
    "children_id" int4 NOT NULL,
    "periods_id" int4 NOT NULL,
    "persons_requests_id" int4 NULL,
    "education_levels_id" int4 NOT NULL,
    "status" "public"."request_status" NOT NULL DEFAULT 'pending',
    "get_loan" bool NOT NULL DEFAULT FALSE,
    "get_pack" bool NOT NULL DEFAULT TRUE,
    "delivery_type" "public"."delivery_type" NULL DEFAULT 'pick_in_plant',
    "plants_id" int4 NULL,
    "responsable_name" varchar NULL,
    "responsable_dni" varchar NULL,
    "responsable_phone" varchar NULL,
    "address" TEXT NULL,
    "address_reference" TEXT NULL,
    "districts_id" int4 NULL,
    "approved_at" timestamptz(0) NULL,
    "approved_by" int4 NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id"),
    UNIQUE ("children_id", "periods_id", "deleted_at")
)
;

CREATE TABLE "public"."children_documents" (
    "id" serial4 NOT NULL,
    "children_requests_id" int4 NOT NULL,
    "file" varchar NOT NULL,
    "type" "public"."document_type" NOT NULL,
    "status" "public"."request_status" NOT NULL DEFAULT 'pending',
    "observation" text NULL,
    "approved_by" int4 NULL,
    "approved_at" timestamptz(0) NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
)
;

CREATE TABLE "public"."users" (
    "id" serial4 NOT NULL,
    "persons_id" int4 NOT NULL,
    "role" "public"."role_types" NOT NULL DEFAULT 'user',
    "password" varchar NOT NULL,
    "salt" varchar(16) NOT NULL,
    "enabled" bool DEFAULT FALSE,
    "verification_token" varchar NULL,
    "verified_at" timestamptz(0) NULL,
    "recovery_token" varchar NULL,
    "last_login" timestamptz(0) NULL,
    "created_at" timestamptz(0) NULL  DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamptz(0) NULL,
    "deleted_at" timestamptz(0) NULL,
    PRIMARY KEY ("id"),
    UNIQUE ("persons_id")
)
;

CREATE TABLE "public"."users_sessions" (
    "id" serial4 NOT NULL,
    "users_id" int4 NOT NULL,
    "token" varchar NOT NULL,
    "expire" timestamptz(0) NOT NULL,
    "ip" varchar NOT NULL,
    "browser" varchar NULL,
    "version" varchar NULL,
    "platform" varchar NULL,
    "created_at" timestamptz(0) DEFAULT CURRENT_TIMESTAMP,
    "closed_at" timestamptz(0) NULL,
    PRIMARY KEY ("id")
)
;

ALTER TABLE "public"."provinces" ADD CONSTRAINT "fk_departments_1" FOREIGN KEY ("departments_id") REFERENCES "public"."departments" ("id");
ALTER TABLE "public"."districts" ADD CONSTRAINT "fk_provinces_1" FOREIGN KEY ("provinces_id") REFERENCES "public"."provinces" ("id");

ALTER TABLE "public"."plants" ADD CONSTRAINT "fk_districts_1" FOREIGN KEY ("districts_id") REFERENCES "public"."districts" ("id");

ALTER TABLE "public"."users" ADD CONSTRAINT "fk_persons_1" FOREIGN KEY ("persons_id") REFERENCES "public"."persons" ("id");
ALTER TABLE "public"."users_sessions" ADD CONSTRAINT "fk_users_1" FOREIGN KEY ("users_id") REFERENCES "public"."users" ("id");

ALTER TABLE "public"."persons" ADD CONSTRAINT "fk_units_1" FOREIGN KEY ("units_id") REFERENCES "public"."units" ("id");
ALTER TABLE "public"."persons" ADD CONSTRAINT "fk_boats_1" FOREIGN KEY ("boats_id") REFERENCES "public"."boats" ("id");

ALTER TABLE "public"."persons_requests" ADD CONSTRAINT "fk_persons_1" FOREIGN KEY ("persons_id") REFERENCES "public"."persons" ("id");
ALTER TABLE "public"."persons_requests" ADD CONSTRAINT "fk_periods_1" FOREIGN KEY ("periods_id") REFERENCES "public"."periods" ("id");

ALTER TABLE "public"."persons_documents" ADD CONSTRAINT "fk_persons_requests_1" FOREIGN KEY ("persons_requests_id") REFERENCES "public"."persons_requests" ("id");

ALTER TABLE "public"."children" ADD CONSTRAINT "fk_persons_1" FOREIGN KEY ("persons_id") REFERENCES "public"."persons" ("id");

ALTER TABLE "public"."children_requests" ADD CONSTRAINT "fk_children_1" FOREIGN KEY ("children_id") REFERENCES "public"."children" ("id");
ALTER TABLE "public"."children_requests" ADD CONSTRAINT "fk_periods_1" FOREIGN KEY ("periods_id") REFERENCES "public"."periods" ("id");
ALTER TABLE "public"."children_requests" ADD CONSTRAINT "fk_persons_requests_1" FOREIGN KEY ("persons_requests_id") REFERENCES "public"."persons_requests" ("id");
ALTER TABLE "public"."children_requests" ADD CONSTRAINT "fk_education_levels_1" FOREIGN KEY ("education_levels_id") REFERENCES "public"."education_levels" ("id");
ALTER TABLE "public"."children_requests" ADD CONSTRAINT "fk_plants_1" FOREIGN KEY ("plants_id") REFERENCES "public"."plants" ("id");
ALTER TABLE "public"."children_requests" ADD CONSTRAINT "fk_districts_1" FOREIGN KEY ("districts_id") REFERENCES "public"."districts" ("id");

ALTER TABLE "public"."children_documents" ADD CONSTRAINT "fk_children_requests_1" FOREIGN KEY ("children_requests_id") REFERENCES "public"."children_requests" ("id");
