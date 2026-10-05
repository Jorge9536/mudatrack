--
-- PostgreSQL database dump
--

\restrict Ufv1mLDuDWPk80S5XcrG1ogrFd052wDuWlJEcwLLuTt16BOiMsd9wpkKPlk6nu3

-- Dumped from database version 18.4
-- Dumped by pg_dump version 18.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: asignacion_personal; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.asignacion_personal (
    id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.asignacion_personal OWNER TO mudatrack_user;

--
-- Name: asignacion_personal_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.asignacion_personal_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.asignacion_personal_id_seq OWNER TO mudatrack_user;

--
-- Name: asignacion_personal_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.asignacion_personal_id_seq OWNED BY public.asignacion_personal.id;


--
-- Name: ayudantes; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.ayudantes (
    id bigint NOT NULL,
    nombre_completo character varying(255) NOT NULL,
    telefono character varying(20) NOT NULL,
    disponible boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ayudantes OWNER TO mudatrack_user;

--
-- Name: ayudantes_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.ayudantes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ayudantes_id_seq OWNER TO mudatrack_user;

--
-- Name: ayudantes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.ayudantes_id_seq OWNED BY public.ayudantes.id;


--
-- Name: bienes; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.bienes (
    id bigint NOT NULL,
    servicio_id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    cantidad integer DEFAULT 1 NOT NULL,
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.bienes OWNER TO mudatrack_user;

--
-- Name: bienes_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.bienes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bienes_id_seq OWNER TO mudatrack_user;

--
-- Name: bienes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.bienes_id_seq OWNED BY public.bienes.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO mudatrack_user;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO mudatrack_user;

--
-- Name: choferes; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.choferes (
    id bigint NOT NULL,
    nombre_completo character varying(255) NOT NULL,
    telefono character varying(20) NOT NULL,
    licencia character varying(20) NOT NULL,
    disponible boolean DEFAULT true NOT NULL,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    user_id bigint
);


ALTER TABLE public.choferes OWNER TO mudatrack_user;

--
-- Name: choferes_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.choferes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.choferes_id_seq OWNER TO mudatrack_user;

--
-- Name: choferes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.choferes_id_seq OWNED BY public.choferes.id;


--
-- Name: clientes; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.clientes (
    id bigint NOT NULL,
    nombre_completo character varying(255) NOT NULL,
    telefono character varying(20) NOT NULL,
    direccion character varying(255),
    latitud numeric(10,7),
    longitud numeric(10,7),
    foto_casa character varying(255),
    observaciones text,
    bloqueado boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.clientes OWNER TO mudatrack_user;

--
-- Name: clientes_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.clientes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.clientes_id_seq OWNER TO mudatrack_user;

--
-- Name: clientes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.clientes_id_seq OWNED BY public.clientes.id;


--
-- Name: configuracion_precios; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.configuracion_precios (
    id bigint NOT NULL,
    precio_la_paz numeric(10,2) DEFAULT '300'::numeric NOT NULL,
    precio_el_alto numeric(10,2) DEFAULT '200'::numeric NOT NULL,
    precio_el_alto_la_paz numeric(10,2) DEFAULT '250'::numeric NOT NULL,
    costo_ayudante numeric(10,2) DEFAULT '80'::numeric NOT NULL,
    costo_piso_adicional numeric(10,2) DEFAULT '20'::numeric NOT NULL,
    costo_callejon numeric(10,2) DEFAULT '30'::numeric NOT NULL,
    costo_km_extra numeric(10,2) DEFAULT '5'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.configuracion_precios OWNER TO mudatrack_user;

--
-- Name: configuracion_precios_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.configuracion_precios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.configuracion_precios_id_seq OWNER TO mudatrack_user;

--
-- Name: configuracion_precios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.configuracion_precios_id_seq OWNED BY public.configuracion_precios.id;


--
-- Name: configuracion_qr; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.configuracion_qr (
    id bigint NOT NULL,
    imagen_qr character varying(255),
    url_qr character varying(255),
    fecha_actualizacion timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.configuracion_qr OWNER TO mudatrack_user;

--
-- Name: configuracion_qr_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.configuracion_qr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.configuracion_qr_id_seq OWNER TO mudatrack_user;

--
-- Name: configuracion_qr_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.configuracion_qr_id_seq OWNED BY public.configuracion_qr.id;


--
-- Name: deudas; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.deudas (
    id bigint NOT NULL,
    cliente_id bigint NOT NULL,
    servicio_id bigint NOT NULL,
    monto numeric(10,2) NOT NULL,
    fecha_vencimiento date NOT NULL,
    estado character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT deudas_estado_check CHECK (((estado)::text = ANY ((ARRAY['pendiente'::character varying, 'pagado'::character varying, 'vencido'::character varying])::text[])))
);


ALTER TABLE public.deudas OWNER TO mudatrack_user;

--
-- Name: deudas_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.deudas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.deudas_id_seq OWNER TO mudatrack_user;

--
-- Name: deudas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.deudas_id_seq OWNED BY public.deudas.id;


--
-- Name: dispositivos; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.dispositivos (
    id bigint NOT NULL,
    dispositivo_id character varying(255) NOT NULL,
    chofer_id bigint NOT NULL,
    vehiculo_id bigint,
    plataforma character varying(255),
    modelo character varying(255),
    activo boolean DEFAULT true NOT NULL,
    ultima_conexion timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.dispositivos OWNER TO mudatrack_user;

--
-- Name: dispositivos_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.dispositivos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.dispositivos_id_seq OWNER TO mudatrack_user;

--
-- Name: dispositivos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.dispositivos_id_seq OWNED BY public.dispositivos.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO mudatrack_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO mudatrack_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO mudatrack_user;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO mudatrack_user;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO mudatrack_user;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO mudatrack_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO mudatrack_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO mudatrack_user;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO mudatrack_user;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO mudatrack_user;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: servicio_ayudante; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.servicio_ayudante (
    id bigint NOT NULL,
    servicio_id bigint NOT NULL,
    ayudante_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.servicio_ayudante OWNER TO mudatrack_user;

--
-- Name: servicio_ayudante_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.servicio_ayudante_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.servicio_ayudante_id_seq OWNER TO mudatrack_user;

--
-- Name: servicio_ayudante_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.servicio_ayudante_id_seq OWNED BY public.servicio_ayudante.id;


--
-- Name: servicios; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.servicios (
    id bigint NOT NULL,
    cliente_id bigint NOT NULL,
    vehiculo_id bigint,
    chofer_id bigint,
    origen character varying(255) NOT NULL,
    destino character varying(255) NOT NULL,
    fecha_servicio date NOT NULL,
    hora_inicio time without time zone,
    hora_fin time without time zone,
    cantidad_ayudantes integer DEFAULT 0 NOT NULL,
    numero_pisos integer DEFAULT 1 NOT NULL,
    es_callejon boolean DEFAULT false NOT NULL,
    costo_total numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    estado character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    metodo_pago character varying(255),
    distancia_km numeric(8,2),
    token_seguimiento character varying(64),
    estado_pago character varying(20) DEFAULT 'pendiente'::character varying,
    CONSTRAINT servicios_estado_check CHECK (((estado)::text = ANY ((ARRAY['pendiente'::character varying, 'confirmado'::character varying, 'en_progreso'::character varying, 'finalizado'::character varying, 'cancelado'::character varying, 'pendiente_pago'::character varying, 'pagado'::character varying])::text[]))),
    CONSTRAINT servicios_metodo_pago_check CHECK (((metodo_pago)::text = ANY ((ARRAY['efectivo'::character varying, 'qr'::character varying, 'transferencia'::character varying])::text[])))
);


ALTER TABLE public.servicios OWNER TO mudatrack_user;

--
-- Name: servicios_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.servicios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.servicios_id_seq OWNER TO mudatrack_user;

--
-- Name: servicios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.servicios_id_seq OWNED BY public.servicios.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO mudatrack_user;

--
-- Name: ubicacion_gps; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.ubicacion_gps (
    id bigint NOT NULL,
    servicio_id bigint NOT NULL,
    latitud numeric(10,7) NOT NULL,
    longitud numeric(10,7) NOT NULL,
    velocidad numeric(5,2),
    fecha_hora timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ubicacion_gps OWNER TO mudatrack_user;

--
-- Name: ubicacion_gps_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.ubicacion_gps_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ubicacion_gps_id_seq OWNER TO mudatrack_user;

--
-- Name: ubicacion_gps_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.ubicacion_gps_id_seq OWNED BY public.ubicacion_gps.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    role character varying(255) DEFAULT 'recepcionista'::character varying NOT NULL,
    google2fa_secret text,
    google2fa_enabled boolean DEFAULT false NOT NULL,
    recovery_codes json,
    CONSTRAINT users_role_check CHECK (((role)::text = ANY ((ARRAY['admin'::character varying, 'recepcionista'::character varying, 'chofer'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO mudatrack_user;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO mudatrack_user;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: vehiculos; Type: TABLE; Schema: public; Owner: mudatrack_user
--

CREATE TABLE public.vehiculos (
    id bigint NOT NULL,
    placa character varying(10) NOT NULL,
    marca character varying(255) NOT NULL,
    modelo character varying(255) NOT NULL,
    tipo character varying(255) NOT NULL,
    capacidad_kg integer NOT NULL,
    disponible boolean DEFAULT true NOT NULL,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT vehiculos_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['3ton'::character varying, '6ton'::character varying, 'chata'::character varying])::text[])))
);


ALTER TABLE public.vehiculos OWNER TO mudatrack_user;

--
-- Name: vehiculos_id_seq; Type: SEQUENCE; Schema: public; Owner: mudatrack_user
--

CREATE SEQUENCE public.vehiculos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehiculos_id_seq OWNER TO mudatrack_user;

--
-- Name: vehiculos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mudatrack_user
--

ALTER SEQUENCE public.vehiculos_id_seq OWNED BY public.vehiculos.id;


--
-- Name: asignacion_personal id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.asignacion_personal ALTER COLUMN id SET DEFAULT nextval('public.asignacion_personal_id_seq'::regclass);


--
-- Name: ayudantes id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.ayudantes ALTER COLUMN id SET DEFAULT nextval('public.ayudantes_id_seq'::regclass);


--
-- Name: bienes id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.bienes ALTER COLUMN id SET DEFAULT nextval('public.bienes_id_seq'::regclass);


--
-- Name: choferes id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.choferes ALTER COLUMN id SET DEFAULT nextval('public.choferes_id_seq'::regclass);


--
-- Name: clientes id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.clientes ALTER COLUMN id SET DEFAULT nextval('public.clientes_id_seq'::regclass);


--
-- Name: configuracion_precios id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.configuracion_precios ALTER COLUMN id SET DEFAULT nextval('public.configuracion_precios_id_seq'::regclass);


--
-- Name: configuracion_qr id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.configuracion_qr ALTER COLUMN id SET DEFAULT nextval('public.configuracion_qr_id_seq'::regclass);


--
-- Name: deudas id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.deudas ALTER COLUMN id SET DEFAULT nextval('public.deudas_id_seq'::regclass);


--
-- Name: dispositivos id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.dispositivos ALTER COLUMN id SET DEFAULT nextval('public.dispositivos_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: servicio_ayudante id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicio_ayudante ALTER COLUMN id SET DEFAULT nextval('public.servicio_ayudante_id_seq'::regclass);


--
-- Name: servicios id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicios ALTER COLUMN id SET DEFAULT nextval('public.servicios_id_seq'::regclass);


--
-- Name: ubicacion_gps id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.ubicacion_gps ALTER COLUMN id SET DEFAULT nextval('public.ubicacion_gps_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: vehiculos id; Type: DEFAULT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.vehiculos ALTER COLUMN id SET DEFAULT nextval('public.vehiculos_id_seq'::regclass);


--
-- Data for Name: asignacion_personal; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.asignacion_personal (id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ayudantes; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.ayudantes (id, nombre_completo, telefono, disponible, created_at, updated_at) FROM stdin;
4	Fernando Flores	8414791	t	2026-08-04 03:23:34	2026-09-07 17:03:47
7	Fernando García	9850029	t	2026-06-19 02:15:12	2026-09-07 17:03:49
6	Javier Torres	6560025	t	2026-07-15 11:29:06	2026-09-07 18:09:21
5	Luis Lima	6765223	t	2026-08-03 19:17:45	2026-09-07 18:09:21
8	Ricardo Rojas	3177587	t	2026-06-16 20:36:08	2026-09-07 18:09:24
9	jorge	78481900	t	2026-09-07 18:10:05	2026-09-07 18:10:05
1	Daniel Lima	6575206	t	2026-06-07 03:54:58	2026-09-07 18:53:40
2	Carlos Flores	5423105	t	2026-06-14 19:24:49	2026-09-14 17:19:28
\.


--
-- Data for Name: bienes; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.bienes (id, servicio_id, nombre, cantidad, descripcion, created_at, updated_at) FROM stdin;
1	1	Mesas	1	\N	2026-07-26 18:37:25	2026-08-11 20:53:14
2	1	Televisor 55"	4	\N	2026-07-26 18:37:25	2026-08-11 20:53:14
3	1	Electrodomésticos	1	\N	2026-07-26 18:37:25	2026-08-11 20:53:14
4	1	Cocina	3	\N	2026-07-26 18:37:25	2026-08-11 20:53:14
5	2	Sofá 3 cuerpos	3	\N	2026-06-17 16:25:55	2026-08-11 20:53:14
6	2	Lavadora	5	\N	2026-06-17 16:25:55	2026-08-11 20:53:14
7	2	Camas	1	\N	2026-06-17 16:25:55	2026-08-11 20:53:14
8	2	Cajas de ropa	5	\N	2026-06-17 16:25:55	2026-08-11 20:53:14
9	3	Cocina	4	\N	2026-07-02 06:21:28	2026-08-11 20:53:14
10	3	Escritorio	1	\N	2026-07-02 06:21:28	2026-08-11 20:53:14
11	4	Camas	1	\N	2026-06-27 20:14:46	2026-08-11 20:53:14
12	4	Televisor 55"	5	\N	2026-06-27 20:14:46	2026-08-11 20:53:14
13	5	Electrodomésticos	1	Facere praesentium incidunt numquam.	2026-06-23 07:00:16	2026-08-11 20:53:14
14	5	Refrigerador	5	\N	2026-06-23 07:00:16	2026-08-11 20:53:14
15	5	Refrigerador	2	\N	2026-06-23 07:00:16	2026-08-11 20:53:14
16	5	Refrigerador	4	\N	2026-06-23 07:00:16	2026-08-11 20:53:14
17	6	Cajas de ropa	3	Voluptatibus dolorem itaque autem vitae.	2026-07-21 11:43:41	2026-08-11 20:53:14
18	6	Cocina	4	Adipisci soluta libero architecto itaque ipsam reiciendis provident non.	2026-07-21 11:43:41	2026-08-11 20:53:14
19	7	Refrigerador	4	Veritatis consequatur dicta labore aut voluptatem molestiae rem.	2026-06-19 18:26:54	2026-08-11 20:53:14
20	7	Electrodomésticos	3	\N	2026-06-19 18:26:54	2026-08-11 20:53:14
21	7	Sofá 3 cuerpos	5	\N	2026-06-19 18:26:54	2026-08-11 20:53:14
22	7	Sillas	1	\N	2026-06-19 18:26:54	2026-08-11 20:53:14
23	8	Televisor 55"	4	\N	2026-06-24 12:08:59	2026-08-11 20:53:14
24	8	Electrodomésticos	4	Maiores illo est quo quia non distinctio eum.	2026-06-24 12:08:59	2026-08-11 20:53:14
25	8	Escritorio	3	\N	2026-06-24 12:08:59	2026-08-11 20:53:14
26	8	Cajas de ropa	5	\N	2026-06-24 12:08:59	2026-08-11 20:53:14
27	9	Escritorio	2	\N	2026-06-18 01:19:21	2026-08-11 20:53:14
28	9	Lavadora	4	\N	2026-06-18 01:19:21	2026-08-11 20:53:14
29	9	Escritorio	2	Quo eligendi et officiis consequatur libero.	2026-06-18 01:19:21	2026-08-11 20:53:14
30	9	Cajas de ropa	4	\N	2026-06-18 01:19:21	2026-08-11 20:53:14
31	10	Mesas	5	\N	2026-08-08 04:54:13	2026-08-11 20:53:14
32	10	Lavadora	5	Sit voluptatem autem labore quas occaecati eaque delectus necessitatibus.	2026-08-08 04:54:13	2026-08-11 20:53:14
33	11	Mesas	5	\N	2026-07-02 16:45:17	2026-08-11 20:53:14
34	11	Mesas	3	\N	2026-07-02 16:45:17	2026-08-11 20:53:14
35	11	Electrodomésticos	2	\N	2026-07-02 16:45:17	2026-08-11 20:53:14
36	12	Lavadora	5	\N	2026-08-05 02:38:35	2026-08-11 20:53:14
37	12	Cocina	5	\N	2026-08-05 02:38:35	2026-08-11 20:53:14
38	12	Cajas de ropa	2	Rerum nemo facilis quo dolor explicabo vitae.	2026-08-05 02:38:35	2026-08-11 20:53:14
39	13	Lavadora	1	\N	2026-07-13 16:25:58	2026-08-11 20:53:14
40	13	Escritorio	2	\N	2026-07-13 16:25:58	2026-08-11 20:53:14
41	13	Mesas	5	Occaecati sint veniam sunt itaque quidem.	2026-07-13 16:25:58	2026-08-11 20:53:14
42	13	Televisor 55"	5	\N	2026-07-13 16:25:58	2026-08-11 20:53:14
43	14	Electrodomésticos	1	Vero sequi ad eum sit quibusdam quia suscipit.	2026-06-25 08:47:37	2026-08-11 20:53:14
44	14	Escritorio	4	Rerum et voluptates repellat est.	2026-06-25 08:47:37	2026-08-11 20:53:14
45	15	Camas	1	Maxime ea nemo odio voluptas earum.	2026-08-09 11:35:17	2026-08-11 20:53:14
46	15	Camas	5	Et saepe sapiente unde velit.	2026-08-09 11:35:17	2026-08-11 20:53:14
47	15	Cajas de ropa	1	\N	2026-08-09 11:35:17	2026-08-11 20:53:14
48	15	Camas	1	Deleniti ut enim repellendus voluptatem.	2026-08-09 11:35:17	2026-08-11 20:53:14
49	16	Televisor 55"	4	\N	2026-07-22 17:02:43	2026-08-11 20:53:14
50	16	Escritorio	5	Pariatur aliquid omnis nisi debitis enim maxime.	2026-07-22 17:02:43	2026-08-11 20:53:14
51	16	Sillas	3	Et in itaque voluptatem odio architecto culpa.	2026-07-22 17:02:43	2026-08-11 20:53:14
52	16	Sofá 3 cuerpos	3	\N	2026-07-22 17:02:43	2026-08-11 20:53:14
53	16	Escritorio	4	\N	2026-07-22 17:02:43	2026-08-11 20:53:14
54	17	Electrodomésticos	3	\N	2026-07-25 00:11:22	2026-08-11 20:53:14
55	17	Cocina	1	\N	2026-07-25 00:11:22	2026-08-11 20:53:14
56	17	Sillas	1	Cumque tempore asperiores aliquam aspernatur necessitatibus voluptatem voluptatibus.	2026-07-25 00:11:22	2026-08-11 20:53:14
57	17	Cajas de ropa	5	Hic id et quis aperiam.	2026-07-25 00:11:22	2026-08-11 20:53:14
58	18	Cajas de ropa	1	\N	2026-06-24 22:36:42	2026-08-11 20:53:14
59	18	Cajas de ropa	1	Id quasi quia architecto quia culpa earum nam.	2026-06-24 22:36:42	2026-08-11 20:53:14
60	18	Ropero	2	\N	2026-06-24 22:36:42	2026-08-11 20:53:14
61	19	Electrodomésticos	5	Sequi ut tenetur quasi esse aliquid a aut ipsum.	2026-06-17 11:07:03	2026-08-11 20:53:14
62	19	Electrodomésticos	3	\N	2026-06-17 11:07:03	2026-08-11 20:53:14
63	19	Lavadora	1	\N	2026-06-17 11:07:03	2026-08-11 20:53:14
64	20	Sillas	5	Velit esse odit sed molestiae.	2026-07-10 13:56:40	2026-08-11 20:53:14
65	20	Ropero	4	\N	2026-07-10 13:56:40	2026-08-11 20:53:14
66	20	Cajas de ropa	3	\N	2026-07-10 13:56:40	2026-08-11 20:53:14
67	21	Mesas	3	Neque placeat non eum qui quae sit enim.	2026-06-25 04:02:13	2026-08-11 20:53:14
68	21	Cajas de ropa	1	Nulla et qui iure necessitatibus sed pariatur dolorem repellat.	2026-06-25 04:02:13	2026-08-11 20:53:14
69	21	Escritorio	5	\N	2026-06-25 04:02:13	2026-08-11 20:53:14
70	22	Cajas de ropa	2	Aut asperiores voluptatibus unde et et labore.	2026-06-16 22:46:31	2026-08-11 20:53:14
71	22	Camas	4	\N	2026-06-16 22:46:31	2026-08-11 20:53:14
72	22	Cocina	2	Aut porro ut molestiae quae.	2026-06-16 22:46:31	2026-08-11 20:53:14
73	22	Cajas de ropa	4	\N	2026-06-16 22:46:31	2026-08-11 20:53:14
74	22	Ropero	5	\N	2026-06-16 22:46:31	2026-08-11 20:53:14
75	23	Mesas	1	Distinctio dolorem beatae et.	2026-06-17 20:04:49	2026-08-11 20:53:14
76	23	Camas	4	\N	2026-06-17 20:04:49	2026-08-11 20:53:14
77	23	Cajas de ropa	4	\N	2026-06-17 20:04:49	2026-08-11 20:53:14
78	23	Ropero	4	\N	2026-06-17 20:04:49	2026-08-11 20:53:14
79	23	Refrigerador	2	\N	2026-06-17 20:04:49	2026-08-11 20:53:14
80	24	Lavadora	1	\N	2026-06-18 16:54:25	2026-08-11 20:53:14
81	24	Televisor 55"	4	\N	2026-06-18 16:54:25	2026-08-11 20:53:14
82	33	5765{´´´´	44	\N	2026-08-13 20:58:47	2026-08-13 20:58:47
83	34	refrigerador	10	\N	2026-08-14 02:49:21	2026-08-14 02:49:21
84	35	refrigerador	1	\N	2026-08-14 02:53:37	2026-08-14 02:53:37
85	35	cosina	2	\N	2026-08-14 02:53:37	2026-08-14 02:53:37
86	35	tele	1	\N	2026-08-14 02:53:37	2026-08-14 02:53:37
87	35	ropero	3	\N	2026-08-14 02:53:37	2026-08-14 02:53:37
88	36	refrigerador	1	delicado	2026-08-14 16:55:47	2026-08-14 16:55:47
89	36	comoda	2	fragiles	2026-08-14 16:55:47	2026-08-14 16:55:47
90	36	ropero	2	\N	2026-08-14 16:55:47	2026-08-14 16:55:47
91	37	refrigerador	2	\N	2026-08-14 23:20:29	2026-08-14 23:20:29
92	37	cosina	2	\N	2026-08-14 23:20:29	2026-08-14 23:20:29
93	38	refrigerador	3	\N	2026-08-15 01:01:55	2026-08-15 01:01:55
94	38	comoda	1	\N	2026-08-15 01:01:55	2026-08-15 01:01:55
95	39	refrigerador	2	\N	2026-08-19 00:49:41	2026-08-19 00:49:41
96	39	cosina	3	\N	2026-08-19 00:49:41	2026-08-19 00:49:41
97	40	comoda	1	\N	2026-08-19 00:57:24	2026-08-19 00:57:24
98	40	bici	1	\N	2026-08-19 00:57:24	2026-08-19 00:57:24
99	41	comoda	1	\N	2026-08-19 00:57:56	2026-08-19 00:57:56
100	42	comoda	1	\N	2026-09-07 16:58:28	2026-09-07 16:58:28
101	43	comoda	1	\N	2026-09-07 17:05:27	2026-09-07 17:05:27
102	44	refrigerador	1	\N	2026-09-07 18:18:04	2026-09-07 18:18:04
103	45	comoda	1	\N	2026-09-07 18:21:26	2026-09-07 18:21:26
104	46	comoda	1	\N	2026-09-07 18:57:33	2026-09-07 18:57:33
105	47	refrigerador	3	delicado	2026-09-10 13:19:13	2026-09-10 13:19:13
106	47	comoda	1	gragil	2026-09-10 13:19:13	2026-09-10 13:19:13
107	47	ropero	4	fragil	2026-09-10 13:19:13	2026-09-10 13:19:13
108	48	comoda	1	\N	2026-09-11 15:50:17	2026-09-11 15:50:17
109	48	comoda	1	\N	2026-09-11 15:50:17	2026-09-11 15:50:17
110	48	ropero	1	\N	2026-09-11 15:50:17	2026-09-11 15:50:17
111	49	refrigerador	1	\N	2026-09-11 16:02:50	2026-09-11 16:02:50
112	49	cosina	1	\N	2026-09-11 16:02:50	2026-09-11 16:02:50
113	51	comoda	1	\N	2026-09-11 17:53:48	2026-09-11 17:53:48
114	52	refrigerador	1	\N	2026-09-11 18:05:17	2026-09-11 18:05:17
115	53	refrigerador	1	\N	2026-09-11 18:09:37	2026-09-11 18:09:37
116	54	comoda	1	\N	2026-09-11 18:15:37	2026-09-11 18:15:37
117	55	refrigerador	1	\N	2026-09-11 14:42:34	2026-09-11 14:42:34
118	56	refrigerador	1	\N	2026-09-11 14:43:35	2026-09-11 14:43:35
119	57	comoda	1	\N	2026-09-11 14:44:34	2026-09-11 14:44:34
120	58	refrigerador	1	\N	2026-09-11 14:45:47	2026-09-11 14:45:47
121	59	refrigerador	1	\N	2026-09-11 15:02:48	2026-09-11 15:02:48
122	60	comoda	1	\N	2026-09-11 15:03:24	2026-09-11 15:03:24
123	62	refrigerador	1	delicado	2026-09-11 15:09:56	2026-09-11 15:09:56
124	63	comoda	1	\N	2026-09-11 15:22:38	2026-09-11 15:22:38
125	64	refrigerador	4	\N	2026-09-11 18:56:09	2026-09-11 18:56:09
126	64	cosinas	3	\N	2026-09-11 18:56:09	2026-09-11 18:56:09
127	65	refrigerador	1	\N	2026-09-14 17:18:58	2026-09-14 17:18:58
128	66	comoda	1	\N	2026-09-14 17:27:38	2026-09-14 17:27:38
129	67	varios	1	\N	2026-09-14 19:48:26	2026-09-14 19:48:26
130	68	refrigerador	1	\N	2026-09-16 22:39:35	2026-09-16 22:39:35
131	69	refrigerador	1	\N	2026-09-22 18:31:54	2026-09-22 18:31:54
132	69	comoda	2	\N	2026-09-22 18:31:54	2026-09-22 18:31:54
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: choferes; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.choferes (id, nombre_completo, telefono, licencia, disponible, observaciones, created_at, updated_at, user_id) FROM stdin;
2	Luis Choque	3257583	64878	f	Ut id enim quae numquam illum minus.	2026-06-20 04:12:24	2026-08-11 20:53:14	\N
3	Roberto Flores	6280848	62836	t	Perferendis sit ea aut consectetur libero et sed.	2026-07-27 04:21:04	2026-08-11 20:53:14	\N
5	José Rojas	3805341	11185	f	Quis dolore id aliquid esse.	2026-08-04 02:40:49	2026-08-11 20:53:14	\N
4	Carlos Torres	1461753	A	t	\N	2026-06-23 13:25:16	2026-08-14 21:50:21	\N
1	David Lima	4229542	26842	f	\N	2026-05-18 07:47:36	2026-09-22 11:32:27	3
\.


--
-- Data for Name: clientes; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.clientes (id, nombre_completo, telefono, direccion, latitud, longitud, foto_casa, observaciones, bloqueado, created_at, updated_at) FROM stdin;
1	Juan Mendoza	5573523	Zona Villa Adela, El Alto	-16.5282755	-68.1487484	\N	In commodi eveniet est temporibus sequi.	t	2026-06-25 23:21:50	2026-08-11 20:53:14
2	Luis Quispe	2583541	Zona Senkata, El Alto	-16.5327049	-68.1203768	\N	Odio esse iure quo facilis consequatur tenetur unde.	f	2026-07-18 03:16:14	2026-08-11 20:53:14
3	Laura García	1931037	Zona Villa Adela, El Alto	-16.4981649	-68.1257502	\N	Asperiores in quidem deserunt autem sed.	f	2026-05-15 07:35:45	2026-08-11 20:53:14
4	Laura Pérez	9724545	Av. 6 de Agosto, La Paz	-16.4843021	-68.1046714	\N	\N	f	2026-07-13 22:07:04	2026-08-11 20:53:14
5	Elena Lima	0361081	Av. Villazón, La Paz	-16.5297788	-68.1437941	\N	Impedit non dicta voluptates quaerat et quibusdam aut repellat.	f	2026-07-21 04:46:10	2026-08-11 20:53:14
6	Sofía García	8543190	Zona Villa Adela, El Alto	-16.4816930	-68.1262607	\N	Provident voluptatem et quo sunt et sunt ducimus nihil.	f	2026-05-14 03:07:24	2026-08-11 20:53:14
7	Luis Mendoza	4978377	Av. Villazón, La Paz	-16.5043696	-68.1110850	\N	Sapiente sed vel voluptatem aliquid sit et harum.	f	2026-06-20 19:55:28	2026-08-11 20:53:14
8	Pedro Mamani	1619413	Calle 12, El Alto	-16.4729200	-68.1576741	\N	Natus sit consectetur ea consequatur.	t	2026-07-24 07:44:53	2026-08-11 20:53:14
9	Jorge Quispe	5930372	Calle 12, El Alto	-16.4804637	-68.1656376	\N	Et temporibus corporis voluptate omnis.	f	2026-06-02 11:20:49	2026-08-11 20:53:14
10	Sofía Torres	3130538	Calle 12, El Alto	-16.5248927	-68.1008141	\N	\N	f	2026-08-01 23:33:13	2026-08-11 20:53:14
12	Carlos Quispe	9291851	Av. Busch, La Paz	-16.5278135	-68.1279922	\N	Sint ut eos velit quidem deleniti vel fuga.	f	2026-08-03 22:42:48	2026-08-11 20:53:14
13	Laura Quispe	6245919	Av. Busch, La Paz	-16.5231220	-68.1378060	\N	Et aut dolorem nihil vitae odit.	f	2026-06-08 19:05:12	2026-08-11 20:53:14
14	Carlos Torres	6812042	Av. Busch, La Paz	-16.5471936	-68.1111158	\N	Ipsa ut ducimus sint.	f	2026-07-01 07:43:05	2026-08-11 20:53:14
17	Jorge Quispe	0579308	Zona Villa Adela, El Alto	-16.5102137	-68.1893975	\N	Laboriosam ratione molestias ut.	f	2026-07-02 08:40:46	2026-08-11 20:53:14
18	María Lima	4529558	Av. Busch, La Paz	-16.4808631	-68.1455879	\N	\N	f	2026-07-22 07:22:38	2026-08-11 20:53:14
20	Sofía Pérez	0506554	Av. Villazón, La Paz	-16.4649733	-68.1269292	\N	\N	f	2026-06-05 03:08:15	2026-08-11 20:53:14
21	Carlos García	8598898	Av. Villazón, La Paz	-16.4946477	-68.1870358	\N	\N	t	2026-05-26 18:32:47	2026-08-11 20:53:14
22	Juan García	6471300	Zona Villa Adela, El Alto	-16.5004574	-68.1789712	\N	Sapiente saepe suscipit reiciendis est.	f	2026-06-05 01:32:51	2026-08-11 20:53:14
24	María Mendoza	6069296	Av. Villazón, La Paz	-16.4964768	-68.1538273	\N	Libero minima voluptatem consectetur pariatur quia et architecto.	f	2026-07-12 08:24:33	2026-08-11 20:53:14
25	Ana Rojas	2620486	Av. Busch, La Paz	-16.5221260	-68.1114149	\N	Possimus magnam qui et.	f	2026-06-07 17:59:43	2026-08-11 20:53:14
15	María Flores	5271212	Av. Busch, La Paz	-16.4606323	-68.1794870	\N	\N	t	2026-07-26 23:23:38	2026-08-11 20:53:14
16	Ana García	3929750	Calle 15, El Alto	-16.4584141	-68.1987605	\N	Qui debitis iste ut deserunt aut eius.	t	2026-06-21 08:33:11	2026-08-11 20:53:14
11	Pedro Lima	8924506	Zona Villa Adela, El Alto	-16.4538103	-68.1923581	\N	Ut possimus pariatur natus blanditiis molestiae.	f	2026-08-11 06:17:52	2026-08-12 17:27:59
26	8765432	gsfgfdfddg	66+´++´+´+	0.0000000	0.0000000	\N	+}}++}}	f	2026-08-13 20:56:35	2026-08-13 20:56:35
27	marcelo ramires duran	12345678	calle 13	\N	\N	\N	\N	f	2026-08-14 02:48:32	2026-08-14 16:30:28
19	María Choque	9871798	Av. Villazón, La Paz	-16.5459130	-68.1701297	\N	Itaque laudantium nemo quaerat aut.	f	2026-08-09 03:48:31	2026-08-14 16:30:31
23	Sofía Choque	7320339	Av. 6 de Agosto, La Paz	-16.4986547	-68.1228006	\N	\N	f	2026-08-06 12:45:06	2026-08-14 16:30:33
28	ariel	78481900	\N	\N	\N	\N	\N	f	2026-08-14 22:22:07	2026-08-14 22:22:07
29	jorge	78481902	\N	\N	\N	\N	\N	f	2026-08-15 00:32:52	2026-08-15 00:33:14
30	jorge	78481922	\N	\N	\N	\N	\N	f	2026-08-15 01:00:51	2026-08-15 01:00:51
31	jorge	78481923	\N	\N	\N	\N	\N	f	2026-08-19 00:48:31	2026-08-19 00:48:31
32	5522	luis	\N	\N	\N	\N	\N	f	2026-08-19 00:53:54	2026-08-19 00:53:54
34	kim	62361780	\N	\N	\N	\N	\N	f	2026-09-10 13:17:14	2026-09-10 13:17:14
35	freddy salcedo	60501168	\N	\N	\N	\N	\N	f	2026-09-11 16:02:01	2026-09-11 16:02:01
36	Shon ce castañeta atahuachi	74267602	zona	\N	\N	\N	\N	f	2026-09-11 18:53:06	2026-09-11 18:53:06
37	ariel caballero	67140414	\N	\N	\N	\N	\N	f	2026-09-14 17:25:02	2026-09-14 17:25:02
38	Luis Aguilar	72586234	ca	\N	\N	\N	prueba	f	2026-09-14 19:46:11	2026-09-16 21:49:48
33	delia salcedo	63158750	alcoreza 36	\N	\N	\N	\N	f	2026-09-07 19:49:42	2026-09-16 21:49:59
39	teniente	71728638	\N	\N	\N	\N	\N	f	2026-09-16 22:38:39	2026-09-16 22:38:39
40	ingeniero	67197816	\N	\N	\N	\N	\N	f	2026-09-22 18:30:34	2026-09-22 18:30:34
\.


--
-- Data for Name: configuracion_precios; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.configuracion_precios (id, precio_la_paz, precio_el_alto, precio_el_alto_la_paz, costo_ayudante, costo_piso_adicional, costo_callejon, costo_km_extra, created_at, updated_at) FROM stdin;
1	300.00	250.00	350.00	100.00	90.00	90.00	20.00	2026-08-11 22:04:14	2026-09-07 22:13:09
\.


--
-- Data for Name: configuracion_qr; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.configuracion_qr (id, imagen_qr, url_qr, fecha_actualizacion, created_at, updated_at) FROM stdin;
1	qrs/rHaHBhvUY5ZpkcWHzZRERrw15ckjd8Qu9uMjc3Se.jpg	\N	2026-09-16 22:17:19	2026-08-14 23:54:31	2026-09-16 22:17:19
\.


--
-- Data for Name: deudas; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.deudas (id, cliente_id, servicio_id, monto, fecha_vencimiento, estado, observaciones, created_at, updated_at) FROM stdin;
2	19	14	680.00	2026-08-12	pendiente	Deuda pendiente de pago	2026-06-25 08:47:37	2026-08-11 20:53:14
3	17	17	250.00	2026-08-14	pendiente	Deuda pendiente de pago	2026-07-25 00:11:22	2026-08-11 20:53:14
4	15	25	300.00	2026-08-20	pendiente	Cliente con deuda pendiente	2026-07-19 14:15:29	2026-08-11 20:53:14
5	15	26	350.00	2026-08-11	pendiente	Cliente con deuda pendiente	2026-07-12 23:18:14	2026-08-11 20:53:14
6	16	27	300.00	2026-08-09	pendiente	Cliente con deuda pendiente	2026-07-16 16:19:07	2026-08-11 20:53:14
7	16	28	300.00	2026-08-06	pendiente	Cliente con deuda pendiente	2026-08-05 11:38:17	2026-08-11 20:53:14
9	19	30	200.00	2026-08-14	pendiente	Cliente con deuda pendiente	2026-08-03 08:35:20	2026-08-11 20:53:14
10	23	31	300.00	2026-08-10	pendiente	Cliente con deuda pendiente	2026-07-18 14:40:21	2026-08-11 20:53:14
11	23	32	250.00	2026-08-13	pendiente	Cliente con deuda pendiente	2026-07-19 22:12:54	2026-08-11 20:53:14
1	7	10	280.00	2026-08-23	pagado	Deuda pendiente de pago	2026-08-08 04:54:13	2026-08-14 03:00:46
8	19	29	200.00	2026-08-02	pagado	Cliente con deuda pendiente	2026-07-20 00:53:36	2026-08-15 00:32:33
12	12	23	750.00	2026-09-12	pendiente	Servicio finalizado sin pago (automático)	2026-09-11 17:38:44	2026-09-11 17:38:44
13	22	1	710.00	2026-09-12	pendiente	Servicio finalizado sin pago (automático)	2026-09-11 17:38:44	2026-09-11 17:38:44
14	2	43	874.00	2026-09-12	pendiente	Servicio finalizado sin pago (automático)	2026-09-11 17:38:44	2026-09-11 17:38:44
15	31	44	668.00	2026-09-12	pendiente	Servicio finalizado sin pago (automático)	2026-09-11 17:38:44	2026-09-11 17:38:44
16	13	45	600.00	2026-09-12	pendiente	Servicio finalizado sin pago (automático)	2026-09-11 17:38:44	2026-09-11 17:38:44
17	9	51	860.00	2026-09-12	pendiente	Servicio finalizado sin pago (automático)	2026-09-11 18:02:19	2026-09-11 18:02:19
18	3	53	300.00	2026-09-12	pendiente	Servicio finalizado sin pago (automático)	2026-09-11 18:14:01	2026-09-11 18:14:01
19	5	54	300.00	2026-09-12	pagado	Servicio finalizado sin pago (automático)	2026-09-11 18:16:01	2026-09-11 18:16:22
20	1	61	100.00	2026-09-12	pendiente	Servicio finalizado sin pago (automático)	2026-09-11 15:34:01	2026-09-11 15:34:01
21	36	64	1066.00	2026-09-13	pendiente	Servicio finalizado sin pago (automático)	2026-09-12 13:36:03	2026-09-12 13:36:03
22	35	49	652.00	2026-09-13	pendiente	Servicio finalizado sin pago (automático)	2026-09-12 13:36:03	2026-09-12 13:36:03
23	3	52	400.00	2026-09-15	pendiente	Servicio finalizado sin pago (automático)	2026-09-14 17:16:01	2026-09-14 17:16:01
24	4	65	1100.00	2026-09-15	pendiente	Servicio finalizado sin pago (automático)	2026-09-14 17:30:00	2026-09-14 17:30:00
25	37	66	1160.00	2026-09-15	pendiente	Servicio finalizado sin pago (automático)	2026-09-14 17:40:00	2026-09-14 17:40:00
26	38	67	400.00	2026-09-23	pendiente	Servicio finalizado sin pago (automático)	2026-09-22 17:53:10	2026-09-22 17:53:10
27	39	68	590.00	2026-09-23	pagado	Servicio finalizado sin pago (automático)	2026-09-22 17:53:13	2026-09-22 18:06:18
28	40	69	680.00	2026-09-23	pendiente	Servicio finalizado sin pago (automático)	2026-09-22 20:03:04	2026-09-22 20:03:04
\.


--
-- Data for Name: dispositivos; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.dispositivos (id, dispositivo_id, chofer_id, vehiculo_id, plataforma, modelo, activo, ultima_conexion, created_at, updated_at) FROM stdin;
3	Fredd2366	3	6	\N	\N	t	\N	2026-09-14 17:25:58	2026-09-22 18:16:32
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_08_10_141233_create_choferes_table	1
5	2026_08_10_141233_create_clientes_table	1
6	2026_08_10_141233_create_vehiculos_table	1
7	2026_08_10_141234_create_ayudantes_table	1
8	2026_08_10_141235_create_servicios_table	1
9	2026_08_10_141236_create_asignacion_personal_table	1
10	2026_08_10_141236_create_bienes_table	1
11	2026_08_10_141236_create_deudas_table	1
12	2026_08_10_141236_create_ubicacion_gps_table	1
13	2026_08_10_141237_create_configuracion_qr_table	1
14	2026_08_10_193655_add_metodo_pago_to_servicios_table	1
15	2026_08_11_205029_add_role_to_users_table	1
16	2026_08_11_215014_create_configuracion_precios_table	2
17	2026_08_11_221944_add_distancia_km_to_servicios_table	3
18	2026_08_14_215329_add_2fa_columns_to_users_table	4
19	2026_09_07_163706_create_servicio_ayudante_table	5
20	2026_09_07_222824_create_dispositivos_table	6
21	2026_09_11_165953_add_token_seguimiento_to_servicios_table	7
22	2026_09_11_145232_change_hora_columns_to_time	8
23	2026_09_22_111731_add_user_id_to_choferes_table	9
24	2026_09_22_113636_create_personal_access_tokens_table	10
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
2	App\\Models\\User	3	test-device	9671b40f31dc0f6f0e59a049173e2385538a8eff7835da77c410b378fa3c9ba0	["*"]	2026-09-22 11:40:36	\N	2026-09-22 11:40:35	2026-09-22 11:40:36
\.


--
-- Data for Name: servicio_ayudante; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.servicio_ayudante (id, servicio_id, ayudante_id, created_at, updated_at) FROM stdin;
1	43	2	2026-09-07 17:39:01	2026-09-07 17:39:01
2	43	1	2026-09-07 17:39:01	2026-09-07 17:39:01
3	43	4	2026-09-07 17:39:01	2026-09-07 17:39:01
4	43	7	2026-09-07 17:39:01	2026-09-07 17:39:01
5	41	2	2026-09-07 18:11:19	2026-09-07 18:11:19
6	41	1	2026-09-07 18:11:19	2026-09-07 18:11:19
7	41	4	2026-09-07 18:11:19	2026-09-07 18:11:19
8	41	7	2026-09-07 18:11:19	2026-09-07 18:11:19
9	41	6	2026-09-07 18:11:19	2026-09-07 18:11:19
10	41	5	2026-09-07 18:11:19	2026-09-07 18:11:19
11	41	8	2026-09-07 18:11:19	2026-09-07 18:11:19
12	41	9	2026-09-07 18:11:19	2026-09-07 18:11:19
13	36	2	2026-09-07 18:13:59	2026-09-07 18:13:59
14	44	5	2026-09-07 18:19:05	2026-09-07 18:19:05
15	44	9	2026-09-07 18:19:05	2026-09-07 18:19:05
16	45	2	2026-09-07 18:23:44	2026-09-07 18:23:44
17	45	1	2026-09-07 18:23:44	2026-09-07 18:23:44
19	45	7	2026-09-07 18:42:16	2026-09-07 18:42:16
20	49	4	2026-09-11 16:07:46	2026-09-11 16:07:46
22	49	8	2026-09-11 16:08:08	2026-09-11 16:08:08
23	51	4	2026-09-11 17:54:20	2026-09-11 17:54:20
24	51	5	2026-09-11 17:54:20	2026-09-11 17:54:20
25	52	4	2026-09-11 18:05:34	2026-09-11 18:05:34
26	63	4	2026-09-11 15:23:05	2026-09-11 15:23:05
27	64	7	2026-09-11 18:59:04	2026-09-11 18:59:04
28	64	6	2026-09-11 18:59:04	2026-09-11 18:59:04
29	64	5	2026-09-11 18:59:04	2026-09-11 18:59:04
30	64	9	2026-09-11 18:59:04	2026-09-11 18:59:04
31	65	4	2026-09-14 17:20:13	2026-09-14 17:20:13
32	65	7	2026-09-14 17:20:13	2026-09-14 17:20:13
33	65	6	2026-09-14 17:20:13	2026-09-14 17:20:13
34	65	5	2026-09-14 17:20:13	2026-09-14 17:20:13
35	65	8	2026-09-14 17:20:13	2026-09-14 17:20:13
36	65	9	2026-09-14 17:20:13	2026-09-14 17:20:13
37	65	1	2026-09-14 17:20:13	2026-09-14 17:20:13
38	65	2	2026-09-14 17:20:13	2026-09-14 17:20:13
39	66	4	2026-09-14 17:29:02	2026-09-14 17:29:02
40	66	7	2026-09-14 17:29:02	2026-09-14 17:29:02
41	66	5	2026-09-14 17:29:02	2026-09-14 17:29:02
42	66	8	2026-09-14 17:29:02	2026-09-14 17:29:02
43	66	1	2026-09-14 17:29:02	2026-09-14 17:29:02
44	67	4	2026-09-14 19:49:54	2026-09-14 19:49:54
45	68	4	2026-09-16 22:40:26	2026-09-16 22:40:26
47	68	1	2026-09-16 22:40:46	2026-09-16 22:40:46
48	69	4	2026-09-22 18:33:14	2026-09-22 18:33:14
49	69	7	2026-09-22 18:33:14	2026-09-22 18:33:14
\.


--
-- Data for Name: servicios; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.servicios (id, cliente_id, vehiculo_id, chofer_id, origen, destino, fecha_servicio, hora_inicio, hora_fin, cantidad_ayudantes, numero_pisos, es_callejon, costo_total, estado, observaciones, created_at, updated_at, metodo_pago, distancia_km, token_seguimiento, estado_pago) FROM stdin;
2	2	3	3	Calle 12, El Alto	Av. Costanera, El Alto	2026-06-28	05:15:19	04:30:15	1	3	f	590.00	cancelado	Delectus labore sit aperiam accusantium quaerat.	2026-06-17 16:25:55	2026-09-11 17:13:08	\N	\N	vmlNTk2keR17mce8888VF2USJ06DEeekPGuz7bRpJf6mQGDP	pendiente
3	7	6	3	Av. 6 de Agosto, La Paz	Calle 15, El Alto	2026-07-04	21:29:34	02:36:06	0	2	f	880.00	pendiente	Est dolor expedita voluptatem.	2026-07-02 06:21:28	2026-09-11 17:13:08	\N	\N	0K4O0z685u2m2ZESb01zG2aAkjZLo8Kdgzao79JiX6mZxqQO	pendiente
5	9	2	1	Zona Villa Adela, El Alto	Ciudad Satélite, El Alto	2026-07-27	12:20:52	\N	0	3	f	550.00	en_progreso	\N	2026-06-23 07:00:16	2026-09-11 17:13:08	\N	\N	KV2LyGMa6MNS48BsaP3Ht1u3G2wAMYhEoTGlsAL90yL2HQf9	pendiente
6	21	1	5	Av. 6 de Agosto, La Paz	Av. Costanera, El Alto	2026-07-28	03:30:53	10:11:12	3	4	f	330.00	pendiente	Dolorem magnam debitis et perspiciatis facilis dolores.	2026-07-21 11:43:41	2026-09-11 17:13:08	\N	\N	URgctgfn5uNmJeb86T4DlGl9ou08ZMdm49kG7h1dfp72Crkt	pendiente
7	23	6	4	Calle 12, El Alto	Sopocachi, La Paz	2026-06-19	01:40:53	03:40:50	2	3	t	630.00	pendiente	\N	2026-06-19 18:26:54	2026-09-11 17:13:08	\N	\N	C4qnjdXqGVFSjmgac0qVae7rZYlsEWgZAbiz5I2tqwPagaHG	pendiente
8	4	6	3	Av. 6 de Agosto, La Paz	Zona Senkata, El Alto	2026-08-02	15:43:53	16:38:17	1	3	f	420.00	cancelado	\N	2026-06-24 12:08:59	2026-09-11 17:13:08	\N	\N	XqXizXF2nSfHlL8nLo5Usv8hq5z0mxl8e6iVWfaiYHCRlLPw	pendiente
13	1	1	1	Miraflores, La Paz	Zona Senkata, El Alto	2026-06-20	15:50:50	\N	1	1	f	300.00	cancelado	Vero molestias voluptatum vero minima corporis.	2026-07-13 16:25:58	2026-09-11 17:13:08	\N	\N	oGEnFjULHfCslJzJsnyIqOqaSNveuUoYNkSx9eEqtKtyLF4E	pendiente
63	6	6	3	VENTA DE, 1895, Avenida Coronel Segundo Bascones, Chualluma, Max Paredes, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Max Paredes, Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	15:30:00	15:40:00	1	1	f	400.00	finalizado	\N	2026-09-11 15:22:38	2026-09-11 15:40:00	efectivo	2.20	QVFBrgQlOdPfx3aYFEWckvaqyuTK6rA7cxQOMgs7IV7OGLl1	pagado
22	22	3	1	Calle 12, El Alto	Av. Costanera, El Alto	2026-07-02	10:36:59	00:05:25	3	2	t	540.00	pendiente	\N	2026-06-16 22:46:31	2026-09-11 17:13:08	\N	\N	k6pxlmFrux3kKNbs2Wp9YQsQ3b64GAi2qYn7PR47ZChKFi9f	pendiente
24	4	3	4	Av. Busch, La Paz	Zona Senkata, El Alto	2026-07-05	19:51:14	16:34:05	3	3	f	440.00	cancelado	\N	2026-06-18 16:54:25	2026-09-11 17:13:08	\N	\N	O1Lb8VBcV0TIrjupXBopxh5fEFCf4Bn1WdhzKr7XMTdzhSue	pendiente
65	4	1	3	Avenida Kollasuyo, Mariscal Santa Cruz, Max Paredes, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Pedro Villamil, Villa Copacabana, San Antonio, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-14	17:22:00	17:30:00	8	1	f	1100.00	finalizado	\N	2026-09-14 17:18:58	2026-09-14 17:30:00	\N	6.40	0Aftfj6OoCpuNJ5CpSV7J3JALOPbkGFo5tK6etQY741redyC	pendiente
33	26	\N	\N	Senda Pairumani-Pico Tunari, Vinto, Quillacollo, Cochabamba, Bolivia	Perú	2026-08-14	\N	\N	6	9	f	25684.00	pendiente	where t65¨[[´{´#$%&/(	2026-08-13 20:58:47	2026-09-11 17:13:08	\N	1214.70	zPh8sDJk1H5AHw95TNq6CmhuaVqnm3ibqk4sd5cK7jqgOD9b	pendiente
18	23	1	1	Zona Villa Adela, El Alto	Sopocachi, La Paz	2026-07-30	19:22:17	\N	0	3	f	510.00	cancelado	\N	2026-06-24 22:36:42	2026-09-22 12:13:01	\N	\N	gh76PswlKxBgzNuOi556jChfp9zS3qlDyr1ro2yD8RgYXEep	pendiente
36	27	1	3	Avenida Julio Cesar Valdez, San Agustín, Cosmos, Distrito 3, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Los Pinos, Los Sauces, Chicani, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-08-15	\N	\N	0	1	f	804.00	confirmado	\N	2026-08-14 16:55:47	2026-09-11 17:13:08	\N	25.20	ZXCyPvmD0hoWmSh5LVgoaWUcDayvKigtH2UIpc9uUyfIfcj8	pendiente
4	9	5	5	Calle 12, El Alto	Av. Costanera, El Alto	2026-08-05	20:51:09	\N	3	3	f	720.00	en_progreso	Nesciunt quia repudiandae sapiente officia expedita.	2026-06-27 20:14:46	2026-09-11 17:38:44	\N	\N	7fzy7hpKfh4jlyjYbosq7kpknlzHDqhy3FfqnMEUOsgFsA14	pendiente
69	40	1	3	Calle Catacora, Ballivián 1ª Sección, Ballivián, Distrito 6, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	General Rosendo Rojas, Villa Victoria, Max Paredes, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-22	18:35:00	20:00:00	2	2	t	680.00	finalizado	\N	2026-09-22 18:31:52	2026-09-22 20:03:01	\N	4.80	TLroxxYVn1NhCKpqoQnrleNYp8A9ZhgM5i9ON9zOQW2JvwWa	pendiente
16	9	4	3	Av. 6 de Agosto, La Paz	Sopocachi, La Paz	2026-07-12	19:38:19	\N	1	4	f	600.00	finalizado	\N	2026-07-22 17:02:43	2026-09-11 17:13:08	\N	\N	kCo0TrmnKTHK3cM4uo8UkXzlkPlSQMfYipZUldQGw7FmqQGU	pendiente
19	2	3	5	Zona Villa Adela, El Alto	Zona Senkata, El Alto	2026-07-16	\N	05:57:53	2	2	f	380.00	finalizado	\N	2026-06-17 11:07:03	2026-09-11 17:13:08	\N	\N	t8ZX9oItu6iAA9kYNxVjQoZlRZp98f2H3evTc0lMuL5yYYbx	pendiente
23	12	1	4	Av. 6 de Agosto, La Paz	Av. Costanera, El Alto	2026-07-20	22:11:08	02:02:35	1	4	f	750.00	finalizado	\N	2026-06-17 20:04:49	2026-09-11 17:38:44	\N	\N	GJqTXVhkHMaASMr5KpIT7eiFlixKvZ5Uxm1ZR7Y7ydj57HYH	pendiente
1	22	6	3	Zona Villa Adela, El Alto	Zona Senkata, El Alto	2026-08-10	\N	04:50:03	1	2	t	710.00	finalizado	\N	2026-07-26 18:37:25	2026-09-11 17:38:44	\N	\N	R5b0YsgIQaaK7QbQtMjKoXFegbAlkOP41LdtWGZEbpXW1e55	pendiente
14	19	6	3	Miraflores, La Paz	Zona Senkata, El Alto	2026-06-15	16:05:10	14:16:04	2	2	t	680.00	finalizado	\N	2026-06-25 08:47:37	2026-09-11 17:13:08	\N	\N	7VShiRQ3NyYYyqj9opVuD05zaPakdLeeW9DzxnnZzQFEsdAr	pendiente
17	17	1	4	Av. Villazón, La Paz	Av. Costanera, El Alto	2026-06-20	14:45:32	\N	0	3	f	250.00	finalizado	\N	2026-07-25 00:11:22	2026-09-11 17:13:08	\N	\N	jzkFW6PzUt8TxYDNKeFc8reS4enLPWfn4zrDsiDDQgeiRKd1	pendiente
26	15	2	1	Av. 6 de Agosto, La Paz	Calle 15, El Alto	2026-08-05	00:46:31	\N	1	2	f	350.00	finalizado	Cliente moroso	2026-07-12 23:18:14	2026-09-11 17:13:08	\N	\N	KfOsBzT9Fk3bpnT6BCIJyNYqeqa23mLEdFnX26CD4VU9psEz	pendiente
27	16	2	5	Av. 6 de Agosto, La Paz	Sopocachi, La Paz	2026-07-24	17:12:15	\N	1	1	f	300.00	finalizado	Cliente moroso	2026-07-16 16:19:07	2026-09-11 17:13:08	\N	\N	PdYUSqVI10Arda8zDY8sQyILKH0kHKfC9oBwGoyvmnb4Z0Cq	pendiente
28	16	4	1	Calle 12, El Alto	Calle 15, El Alto	2026-07-15	05:58:49	\N	2	2	f	300.00	finalizado	Cliente moroso	2026-08-05 11:38:17	2026-09-11 17:13:08	\N	\N	bMKOCqsyPhX55RhYwAYUH4gqeyOrwC71z6OXC9mKhYM6OFsp	pendiente
48	28	\N	\N	Avenida Junin, Villa Adela, Distrito 3, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	BOL-110, Calle Rosendo Villalobos, Miraflores, Centro, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-12	08:00:00	10:00:00	3	3	t	958.00	pendiente	\N	2026-09-11 15:50:17	2026-09-11 17:13:08	\N	14.40	I2BqEjUee5ddKgvuqfY2cdrYfIL1OEShvENGuzDaCIC2yNug	pendiente
64	36	6	4	Distrito 7, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Roberto Prudencio Romerin, San Muguel, Calacoto, Sur, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-12	08:00:00	10:00:00	4	1	f	1066.00	finalizado	\N	2026-09-11 18:56:09	2026-09-12 13:36:03	\N	28.30	0brHPdUmsOTNJRzYK9ieat2jE36Wv6rMSyxe5ucntYLy0Bo2	pendiente
15	19	3	5	Av. Busch, La Paz	Av. Costanera, El Alto	2026-06-14	06:30:41	16:09:25	3	1	f	500.00	cancelado	Velit quis omnis qui autem autem.	2026-08-09 11:35:17	2026-09-11 17:13:08	\N	\N	KZROwdqbsy43eTJPTHJaREOkCaq9jMvoY59RWeHb56aysW2S	pendiente
49	35	1	3	Viacha, Ingavi, La Paz, Bolivia	Avenida Julio Téllez, Las Nieves (Cotahuma), Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-12	08:00:00	13:00:00	2	1	f	652.00	finalizado	\N	2026-09-11 16:02:50	2026-09-12 13:36:03	\N	17.60	9uFCpnU0hsrDXRRBZ8FmL30zDG3qTWQvOBa0Tr6P38Q6izCk	pendiente
52	3	1	4	Calle 21 de Mayo, Kenani Pata La Hoyada, Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Canónigo Ayllón, Alto San Pedro, Sopocachi, Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-12	14:08:00	15:00:00	1	1	f	400.00	finalizado	\N	2026-09-11 18:05:17	2026-09-14 17:16:01	\N	4.70	iXQUEQlrFEnX4OW9sK42vtz4bOf3nlnW14Q1DpPiIZm1xhxo	pendiente
35	2	1	3	Calle 7, Santiago II, Distrito 2, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Pantini, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-08-15	\N	02:55:07	12	22	t	3782.00	confirmado	tele delicado de plasma	2026-08-14 02:53:37	2026-09-11 17:13:08	efectivo	15.10	fPfixwju14uh9LGTPGomVLTVM6EIJXx773WFQbutFK970BQH	pendiente
38	30	1	3	Calle 7 La Plata, FerroPetrol, 16 de Julio, Distrito 6, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Avenida Ernesto Guevara, Los Sauces, Cheka Chinchaya Alto, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-08-16	\N	\N	2	2	t	808.00	confirmado	\N	2026-08-15 01:01:55	2026-09-11 17:13:08	\N	16.40	TAVHJPGlkLiweFL8OJ5xCdTZ6PZ6w4rtz31ZFUVgFWH9zhWY	pendiente
47	34	\N	\N	Puente Elizardo Pérez, Villa San Juan, Distrito 5, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Las Palmeras, Valle de las Flores, San Antonio, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	18:00:00	20:00:00	2	1	t	694.00	pendiente	\N	2026-09-10 13:19:13	2026-09-11 17:13:08	\N	15.20	BwRmfU9DIbjA6dDjHxxsuzRnkVT2bbbIhmxsgrGQniGZIWY8	pendiente
66	37	1	3	Calle Cuarto Centenario, Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	San Juan, Periférica, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-14	17:30:00	17:40:00	5	4	t	1160.00	finalizado	\N	2026-09-14 17:27:38	2026-09-14 17:40:00	\N	4.30	FMbPRwaBDq0v8cPFUnQgL1oj7ljaki3rCdLb2gjE5gz0R2CL	pendiente
41	32	1	3	Chonchocoro, Viacha, Ingavi, La Paz, Bolivia	Palacio del Deporte, Calle 2, Alto Obrajes Sector D, Obrajes, Sur, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-08-28	\N	\N	7	1	f	1286.00	confirmado	\N	2026-08-19 00:57:56	2026-09-11 17:13:08	\N	24.30	JEG0T6VElkyo47CcqznX7hJB231awikdzQgMpEgsrto2AV3A	pendiente
43	2	1	3	Calle 104, Bolívar D, Distrito 1, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Villazon, Valle de las Flores, San Antonio, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-08	08:00:00	10:00:00	3	2	t	874.00	finalizado	\N	2026-09-07 17:05:27	2026-09-11 17:38:44	\N	14.70	VzcioErjpGeqveGfJ9n9XxtUduRWSB2NeUZjiQICDf1S1tZf	pendiente
44	31	3	4	Centro Medico De La Piel, Avenida 6 de Marzo, Ampliación Ferroviaria 2da. Sección, 16 de Julio, Distrito 6, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Federico Garcia Lorca, Primavera, San Antonio, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-08	08:00:00	09:00:00	2	2	f	668.00	finalizado	\N	2026-09-07 18:18:04	2026-09-11 17:38:44	\N	13.90	o3eRyJe7c9MPKdqrPWvEEFxnsqVND88lNXl2a7JmEUF7PYRz	pendiente
45	13	1	3	Calle 9, Bolivar A, Distrito 1, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	San Antonio, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-08	12:00:00	15:00:00	3	1	f	600.00	finalizado	\N	2026-09-07 18:21:26	2026-09-11 17:38:44	\N	10.00	xtAjvthmMpOtCC297PJMANHESQrF3My8JthtHCYaBs1nClqy	pendiente
51	9	1	3	Calle Santa Rosa del Abuná, San Nicolás, Distrito 3, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Los Sauces, Chicani, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	14:00:00	16:00:00	2	1	t	860.00	finalizado	\N	2026-09-11 17:53:48	2026-09-11 18:02:19	\N	23.50	k3dgVvCsS9WdP6TkBfpSivl76l8Aqggk8QOWhRur8y5d0gb1	pendiente
25	15	3	3	Av. 6 de Agosto, La Paz	Sopocachi, La Paz	2026-07-30	15:40:50	\N	1	1	f	300.00	finalizado	Cliente moroso	2026-07-19 14:15:29	2026-09-11 17:13:08	\N	\N	7dQzrYgP4FV2qObDGZcacaZZ9eUSG6mck8Mv9FVBwazd3vsH	pendiente
30	19	1	4	Calle 12, El Alto	Sopocachi, La Paz	2026-07-15	11:55:03	\N	3	2	f	200.00	finalizado	Cliente moroso	2026-08-03 08:35:20	2026-09-11 17:13:08	\N	\N	LvkxVYoqxOuz9UPeI5slFP7dUAALooFDU6fEeXmZzzADF4cY	pendiente
53	3	3	4	Calle 4, Tejada Rectangular, Distrito 1, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Avenida Tejada Sorzano, Miraflores Alto, Periférica, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	14:11:00	14:39:00	0	1	f	300.00	finalizado	\N	2026-09-11 18:09:37	2026-09-11 18:14:01	\N	7.60	ZEmO1XlqDYwG94pTqsEM7BLLnG6SYFEYPRYnyaKqbX9HzGTq	pendiente
55	5	\N	\N	Avenida Buenos Aires, 14 de Septiembre, Max Paredes, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Agua de la Vida Norte, Periférica, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-12	14:45:00	15:00:00	0	1	f	300.00	pendiente	\N	2026-09-11 14:42:34	2026-09-11 14:42:34	\N	5.10	vyQb76o6PDzCKZwmLhYT7gi4HdVRq5pJN5tjhUQXVzQX4G6Z	pendiente
56	6	\N	\N	Calle 10, PLAN 129, Distrito 1, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Alto Obispo Bosque, Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	14:46:00	15:00:00	0	1	f	300.00	pendiente	\N	2026-09-11 14:43:35	2026-09-11 14:43:35	\N	4.80	stMAgKRHtSsC5RN2R1WVWO7qlRFopWWBmSykQ99xpAf0lKq0	pendiente
57	7	\N	\N	Avenida Apumalla, Challampaya, Max Paredes, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Jorge Saenz, Miraflores, Centro, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	14:50:00	15:00:00	1	1	f	400.00	pendiente	\N	2026-09-11 14:44:34	2026-09-11 14:44:34	\N	4.30	GqnHyNAuYS4MN3oZkxoxcOUEAHBAgi6rCaBhiOu7aM1GFTqw	pendiente
58	4	\N	\N	Luci-Fer taller de costura, 1101, calle JUAN BORJA, Alto Chamoco Chico, Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Agua de la Vida, Periférica, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	14:50:00	15:00:00	0	1	f	300.00	pendiente	\N	2026-09-11 14:45:47	2026-09-11 14:45:47	\N	4.90	72cSKw35VKmSiB1xqkEXt6chKYQRPU3DBPhogC494QUIAg8J	pendiente
67	38	1	3	Hospital Metodista, Calle Feliz Ventemillas, Obrajes, Sur, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle 1, Irpavi Bajo, Irpavi, Calacoto, Sur, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-18	10:00:00	11:00:00	1	1	f	400.00	finalizado	\N	2026-09-14 19:48:26	2026-09-22 17:53:07	\N	0.00	upkzZBNqMWwF2fXA6mfxbDTxYiShcVRz970t5kOoD6BtImdY	pendiente
59	6	\N	\N	Calle Puertas, Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Kilómetro Cero, Calle Ayacucho, Central, Centro, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	14:56:00	15:00:00	1	1	f	400.00	pendiente	\N	2026-09-11 15:02:48	2026-09-11 15:02:48	\N	2.50	2SwzQV2lZcypQNMt6KVoZ5OXLbCnuVlQqErB41uLp2Qegxnn	pendiente
60	29	\N	\N	Calle La Paz, Las Nieves (Cotahuma), Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Chuquisaca, San Sebastián, Centro, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	15:10:00	16:00:00	1	1	f	400.00	pendiente	\N	2026-09-11 15:03:24	2026-09-11 15:03:24	\N	3.10	sr3BThf8Q5pgHiqKAdkFZ360BQ2gLY3gcep80Ekn30a7IEjz	pendiente
61	1	1	1	PRUEBA SCHEDULER	PRUEBA SCHEDULER	2026-09-11	15:06:00	15:34:00	0	1	f	100.00	finalizado	\N	2026-09-11 15:04:21	2026-09-11 15:34:00	\N	0.00	BSS2e2IPRawrTt53oRErh4SzCAuikx5RZ7cUY2JdjYntPAk4	pendiente
68	39	1	3	Calle Monje Ortiz, Nueva Asunción Unidad Vecinal 2, Villa Ingenio, Distrito 5, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	U. E. Juan Carlos Flores Bedregal, Calle Jallucallta, Estrella de Belén, Distrito 4, El Alto, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-17	08:00:00	09:00:00	2	1	t	590.00	finalizado	\N	2026-09-16 22:39:35	2026-09-22 18:06:15	efectivo	5.10	jkbltLBu4zpyzUhlmt1ivpb5Wrb8Gs6PMJNAS3POxcvmC5iv	pagado
32	23	5	4	Av. 6 de Agosto, La Paz	Calle 15, El Alto	2026-07-29	11:51:44	\N	3	2	f	250.00	finalizado	Cliente moroso	2026-07-19 22:12:54	2026-09-11 17:13:08	\N	\N	DV7aGcduPFcZrSbE5FGJX5wCqxEL2b1ACelwpXJgxUytUqRn	pendiente
50	1	1	1	Prueba Origen	Prueba Destino	2026-09-11	17:41:00	17:51:27	0	1	f	100.00	finalizado	\N	2026-09-11 17:40:49	2026-09-11 17:51:27	efectivo	0.00	MfCxOCEsvqZP8IMzfYL8cU69jrypTDbtz22d96dCT3cplLA4	pagado
9	18	1	1	Miraflores, La Paz	Calle 15, El Alto	2026-07-13	\N	\N	3	2	f	640.00	finalizado	\N	2026-06-18 01:19:21	2026-09-11 17:13:08	efectivo	\N	weL7znQkKLKwrN74Cx6p1iTvyfHx5Uph6QpPeqva5KpYM7dX	pagado
20	16	1	4	Calle 12, El Alto	Calle 15, El Alto	2026-06-17	\N	\N	2	2	f	530.00	finalizado	\N	2026-07-10 13:56:40	2026-09-11 17:13:08	efectivo	\N	ZgBMVEKIgKN47FmQpBBTm0YlLBYNcHnoOrosUsAcvjMMBmNU	pagado
34	27	\N	\N	Zona 23 de Marzo La Hoyada, Max Paredes, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Santa Rosa, Periférica, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-08-15	\N	02:50:20	2	2	t	880.00	finalizado	\N	2026-08-14 02:49:21	2026-09-11 17:13:08	efectivo	3.90	QSdSEGIeta3jTtAsfcTFCgYcsL1v2yUxvBjtEyc7Lsm63tA4	pagado
10	7	1	2	Miraflores, La Paz	Av. Costanera, El Alto	2026-06-22	15:32:54	03:00:46	2	4	f	280.00	finalizado	In sunt perferendis quibusdam ut nihil totam commodi corporis.	2026-08-08 04:54:13	2026-09-11 17:13:08	efectivo	\N	oGMzA6Feh9SYAmeN8xrOYezUL6Zt7wSxrBvi4BNnOtnEJCgT	pagado
37	28	1	3	Calle 3 de Mayo, Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Calixto Ascarrunz, Valle Hermoso, San Antonio, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-08-15	\N	00:16:20	2	2	f	590.00	finalizado	\N	2026-08-14 23:20:29	2026-09-11 17:13:08	efectivo	5.70	uuObmKdRY314tOZCryeu6V8SA7rZjQsptg4gnYYqxGJPhGoC	pagado
29	19	5	4	Calle 12, El Alto	Calle 15, El Alto	2026-07-19	21:19:02	00:32:33	1	1	f	200.00	finalizado	Cliente moroso	2026-07-20 00:53:36	2026-09-11 17:13:08	efectivo	\N	Xta1DX4klQsH2QvepFRMEwWAsb8kQbwKZFNwBDmQCX1lDnUi	pagado
11	17	4	1	Av. 6 de Agosto, La Paz	Zona Senkata, El Alto	2026-07-06	02:51:38	17:16:35	2	4	f	680.00	finalizado	\N	2026-07-02 16:45:17	2026-09-11 17:13:08	efectivo	\N	qsU2olAXypJpk8W1ICeFhS9SeM2UWtCnnbef4DOiqJdem8tk	pagado
21	19	6	1	Av. 6 de Agosto, La Paz	Ciudad Satélite, El Alto	2026-08-09	07:42:14	11:15:25	3	4	f	500.00	finalizado	\N	2026-06-25 04:02:13	2026-09-11 17:13:08	efectivo	\N	6NrQkeHV7hHg1BoDWYzc29xR9WhwpYcFwCu4oQP4sGxzjJJz	pagado
12	6	5	4	Av. Busch, La Paz	Av. Costanera, El Alto	2026-07-28	11:26:58	02:25:18	3	2	f	630.00	finalizado	Et et est expedita consequatur aut vitae rem.	2026-08-05 02:38:35	2026-09-11 17:13:08	efectivo	\N	zHj50T8cdd45wjCAmDLlE62FUKoqelOZazqlZsqPRyIHYKjK	pagado
39	31	6	3	San Dental, Plaza Libano, Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Calle Eusebio Gutiérrez, Barrio Gráfico, Periférica, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-08-21	\N	00:50:58	2	2	t	680.00	finalizado	\N	2026-08-19 00:49:41	2026-09-11 17:13:08	efectivo	4.50	M3DCm6k59IlwRk6dTN5jDhg8jLDiCio2Z77LnWaQ2b59hRlF	pagado
40	32	1	3	Puchucollo Bajo, Laja, Provincia Los Andes, La Paz, Bolivia	Primavera, Cheka Chinchaya Alto, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-08-28	\N	01:00:51	3	1	f	1032.00	finalizado	\N	2026-08-19 00:57:24	2026-09-11 17:13:08	efectivo	31.60	FQbK0vcvB4bZphbus62AZjPVn9KkeIwJmRjJ4iphWKo8Otde	pagado
42	2	1	3	Kochapampa, Periférica, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Kalajahuira, Periférica, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-08	12:30:00	17:04:22	2	2	t	680.00	finalizado	\N	2026-09-07 16:58:28	2026-09-11 17:13:08	efectivo	2.00	R5R7RQMNqipR33vN0LDnplphDu4eA3NaBabClmyaentZsOzZ	pagado
46	27	\N	\N	Contorno Bajo, Viacha, Ingavi, La Paz, Bolivia	Municipio La Asunta, Provincia Sud Yungas, La Paz, Bolivia	2026-09-08	08:00:00	18:58:26	0	1	f	4130.00	finalizado	\N	2026-09-07 18:57:33	2026-09-11 17:13:08	efectivo	201.50	7fqQRcPe5JEpDQ4MOUsi4UA4Q8wBlcgSTmQ0vRwPablT79h6	pagado
54	5	6	4	Calle Nueva America, Las Nieves (Cotahuma), Cotahuma, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	Villa Pabón, Periférica, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	14:20:00	18:16:22	0	1	f	300.00	finalizado	\N	2026-09-11 18:15:37	2026-09-11 18:16:22	efectivo	5.00	Ik14gH4eEZxc2FzrasoWkcMWxR3ZchAdycFmIsO4vRHg5Eul	pagado
62	5	6	3	Calle Ricardo Bustamante, Max Paredes, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	668, Calle Colón, Central, Centro, La Paz, Provincia Pedro Domingo Murillo, La Paz, Bolivia	2026-09-11	15:15:00	15:30:00	0	1	f	300.00	finalizado	\N	2026-09-11 15:09:56	2026-09-11 15:10:27	efectivo	3.00	OwKU0phUOvus01gWayFdnjNl086nHhYyYGBPggA0QbaMw1fh	pagado
31	23	2	4	Av. 6 de Agosto, La Paz	Calle 15, El Alto	2026-08-10	11:15:11	\N	1	1	t	300.00	finalizado	Cliente moroso	2026-07-18 14:40:21	2026-09-11 17:13:08	\N	\N	bmn29zwjjZT6iBhnlFvPkZlQNf41xJaETlMrcTaaoBvhaTEI	pendiente
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
WYfXJkT33pa4qOkKmUL1QG4dXCjwH9K2se9pZMa8	1	172.174.3.253	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0	YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSUExVXZNSUVUYkFEb3RjdFRrS2x3eG9LN21NWlNnN21jRGhrMjdmZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xNzIuMTc0LjMuMjUzOjgwMDAvc2VydmljaW9zIjtzOjU6InJvdXRlIjtzOjE1OiJzZXJ2aWNpb3MuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9	1790118589
kZ2VOXVrHzcAGDilnnpRmc8y7gEB7u9WZ70jAzRb	\N	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; es-BO) WindowsPowerShell/5.1.26100.9444	YTo0OntzOjY6Il90b2tlbiI7czo0MDoidEV5SGZ5YkxaRXd2eDltcUpIVW9iTXpMd0xRc1dUcG54amtGS2l1RiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO319	1790371931
z7Z9pvbA4sSKEGgL7kmJfRnHGqPBg8KVHxKkC6Gw	\N	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; es-BO) WindowsPowerShell/5.1.26100.9444	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYWwzMERycmNwOWUyWk0zUm1McGFrWmtMU1VVblFXY2tuNmhDTWRmVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO319	1790372138
7f7S17qpXGlp1h7hFxVzPAryLW2aXCd74eDPhEe0	1	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0	YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWnVNZFFObjhyNlhMTXFPSVd6d1hpZXdab085bjhLb1VuODJ5NFpzZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czozOiJ1cmwiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==	1791240785
yv2SaufbCfuRMbSh4dC2GBYJezkiDrSLbOPXf6WS	1	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVEpqV2RMdFF0VzdBVjJOSFZHT2pPclVXOWl2OTBJUlpkM1JucDl1ZCI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3NlcnZpY2lvcyI7czo1OiJyb3V0ZSI7czoxNToic2VydmljaW9zLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1790113997
D9hOL0fyH1EFMhoUVqdu47MiNvvl7gstm76sO8ab	1	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0	YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNTZ0TUhTaThZSUhOVHpJN1BRTUF3RDBtWmtxd0lDeGJTQkJ0b3haZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjE6Imh0dHA6Ly9jaHJ5c3RhbC1pZ25vcmFibGUtY2xlbWVudGluZS5uZ3Jvay1mcmVlLmRldi9zZXJ2aWNpb3MiO3M6NToicm91dGUiO3M6MTU6InNlcnZpY2lvcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=	1790114002
u1jM5S5OOIlxnvMjKfWoDWzWQZW5Ipu6jhJF85hS	1	172.174.3.108	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0	YTo2OntzOjY6Il90b2tlbiI7czo0MDoiN01MbnVoODlCbGY2NVNFSGp3OFBkbXpNTU12U2NYdmM2a0RIN09hRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xNzIuMTc0LjMuMTA4OjgwMDAvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTA6IjJmYV9zZWNyZXQiO3M6MTY6IlVXSURURklLTVpFSE5ISk0iO30=	1790116957
xhGNIERwjx7MoUqBCRsx9NFXuEI8ktnHIvEYdMtt	\N	172.172.9.197	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36 OPR/101.0.0.0	YTo0OntzOjY6Il90b2tlbiI7czo0MDoidFZLUmRNb0F2T0c2c2d0cWVHU285MGtWTFFSTkhRMlhoaEFlcmMyWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xNzIuMTc0LjMuMTA4OjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjM1OiJodHRwOi8vMTcyLjE3NC4zLjEwODo4MDAwL2Rhc2hib2FyZCI7fX0=	1790115523
\.


--
-- Data for Name: ubicacion_gps; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.ubicacion_gps (id, servicio_id, latitud, longitud, velocidad, fecha_hora, created_at, updated_at) FROM stdin;
1	69	-16.5345859	-68.0866081	0.00	2026-09-22 18:35:04	2026-09-22 18:35:04	2026-09-22 18:35:04
2	69	-16.5345443	-68.0865705	0.00	2026-09-22 18:36:02	2026-09-22 18:36:02	2026-09-22 18:36:02
3	69	-16.5346352	-68.0865426	0.00	2026-09-22 18:37:02	2026-09-22 18:37:02	2026-09-22 18:37:02
4	69	-16.5346323	-68.0866097	0.00	2026-09-22 18:38:03	2026-09-22 18:38:03	2026-09-22 18:38:03
5	69	-16.5346373	-68.0866412	0.00	2026-09-22 18:39:03	2026-09-22 18:39:03	2026-09-22 18:39:03
6	69	-16.5346284	-68.0866672	0.00	2026-09-22 18:40:02	2026-09-22 18:40:02	2026-09-22 18:40:02
7	69	-16.5346479	-68.0865292	0.00	2026-09-22 18:41:03	2026-09-22 18:41:03	2026-09-22 18:41:03
8	69	-16.5345670	-68.0866905	0.00	2026-09-22 18:42:02	2026-09-22 18:42:02	2026-09-22 18:42:02
9	69	-16.5346603	-68.0865857	0.00	2026-09-22 18:43:02	2026-09-22 18:43:02	2026-09-22 18:43:02
10	69	-16.5346228	-68.0866121	0.00	2026-09-22 18:44:02	2026-09-22 18:44:02	2026-09-22 18:44:02
11	69	-16.5345887	-68.0866477	0.00	2026-09-22 18:45:02	2026-09-22 18:45:02	2026-09-22 18:45:02
12	69	-16.5346181	-68.0866749	0.00	2026-09-22 18:46:02	2026-09-22 18:46:02	2026-09-22 18:46:02
13	69	-16.5345935	-68.0867074	0.00	2026-09-22 18:47:02	2026-09-22 18:47:02	2026-09-22 18:47:02
14	69	-16.5345521	-68.0866308	0.00	2026-09-22 18:48:02	2026-09-22 18:48:02	2026-09-22 18:48:02
15	69	-16.5345972	-68.0867191	0.00	2026-09-22 19:05:05	2026-09-22 19:05:05	2026-09-22 19:05:05
16	69	-16.5346528	-68.0866228	0.00	2026-09-22 19:06:04	2026-09-22 19:06:04	2026-09-22 19:06:04
17	69	-16.5346352	-68.0866391	0.00	2026-09-22 19:07:04	2026-09-22 19:07:04	2026-09-22 19:07:04
18	69	-16.5345716	-68.0866451	0.00	2026-09-22 19:08:03	2026-09-22 19:08:03	2026-09-22 19:08:03
19	69	-16.5345746	-68.0866587	0.00	2026-09-22 19:09:04	2026-09-22 19:09:04	2026-09-22 19:09:04
20	69	-16.5345386	-68.0867317	0.00	2026-09-22 19:10:03	2026-09-22 19:10:03	2026-09-22 19:10:03
21	69	-16.5345390	-68.0866973	0.00	2026-09-22 19:11:04	2026-09-22 19:11:04	2026-09-22 19:11:04
22	69	-16.5346521	-68.0866081	0.00	2026-09-22 19:12:02	2026-09-22 19:12:02	2026-09-22 19:12:02
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, role, google2fa_secret, google2fa_enabled, recovery_codes) FROM stdin;
2	Recepcionista	recepcionista@mudatrack.com	\N	$2y$12$JVGnPIrqrMdgw26npXH7GeHxAD3eQ/AUQoiuUm.PMETVp6ze4rg3e	\N	2026-08-11 21:39:03	2026-08-11 21:39:03	recepcionista	\N	f	\N
4	jorge	jorge@gmail.com	\N	$2y$12$i4uPhzqJuj.ka2yHwCacbuB15BycOin5Ckh5NMCj7eW.T.n01P0z2	\N	2026-08-14 01:37:38	2026-08-14 01:37:38	recepcionista	\N	f	\N
3	Chofer Juan	chofer@mudatrack.com	\N	$2y$12$fXTD1YyMCRizC4GjVbPzRuo33PFyqjtDxKHhZN2kPDiFP6ZV23eAG	\N	2026-08-11 21:39:11	2026-09-22 11:31:52	chofer	\N	f	\N
1	Admin MudaTrack	admin@mudatrack.com	\N	$2y$12$RfuB2gJBvFYHi4Z6X98Zje2PwLN7BQl8amay2w/hF9u61Kg57Gy6u	uP9DURzER0pT989VKXyOL7UIXk3VnhGIr5OBxHwaVLI6BdQMEfXokOjVCgTa	2026-08-11 20:53:14	2026-08-15 01:24:27	admin	\N	f	\N
\.


--
-- Data for Name: vehiculos; Type: TABLE DATA; Schema: public; Owner: mudatrack_user
--

COPY public.vehiculos (id, placa, marca, modelo, tipo, capacidad_kg, disponible, observaciones, created_at, updated_at) FROM stdin;
1	GCX-KZJ	Mitsubishi	Transit	3ton	3000	t	Et occaecati dolores ut ut.	2026-07-16 22:57:49	2026-08-11 20:53:14
2	JDV-TNI	Isuzu	F-150	chata	3000	f	Qui numquam aliquam voluptatibus provident consequatur labore expedita.	2026-08-07 11:39:31	2026-08-11 20:53:14
4	O9F-KRB	Toyota	Hilux	3ton	1500	f	Est temporibus dolores repellat consectetur ex architecto.	2026-07-19 23:33:02	2026-08-11 20:53:14
5	K2P-LGC	Mitsubishi	Transit	3ton	1500	f	Est doloribus aut itaque autem quia veritatis.	2026-06-11 08:58:21	2026-08-11 20:53:14
6	YJG-DGS	Ford	NPR	6ton	8000	t	\N	2026-05-17 09:47:48	2026-08-11 20:53:14
3	6UN-Z5V	Mitsubishi	NPR	3ton	1500	t	Eos at odio veritatis possimus.	2026-05-28 00:40:07	2026-08-14 02:42:12
\.


--
-- Name: asignacion_personal_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.asignacion_personal_id_seq', 1, false);


--
-- Name: ayudantes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.ayudantes_id_seq', 9, true);


--
-- Name: bienes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.bienes_id_seq', 132, true);


--
-- Name: choferes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.choferes_id_seq', 5, true);


--
-- Name: clientes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.clientes_id_seq', 40, true);


--
-- Name: configuracion_precios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.configuracion_precios_id_seq', 1, true);


--
-- Name: configuracion_qr_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.configuracion_qr_id_seq', 1, true);


--
-- Name: deudas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.deudas_id_seq', 28, true);


--
-- Name: dispositivos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.dispositivos_id_seq', 3, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.migrations_id_seq', 24, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 2, true);


--
-- Name: servicio_ayudante_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.servicio_ayudante_id_seq', 49, true);


--
-- Name: servicios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.servicios_id_seq', 69, true);


--
-- Name: ubicacion_gps_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.ubicacion_gps_id_seq', 22, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.users_id_seq', 4, true);


--
-- Name: vehiculos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mudatrack_user
--

SELECT pg_catalog.setval('public.vehiculos_id_seq', 6, true);


--
-- Name: asignacion_personal asignacion_personal_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.asignacion_personal
    ADD CONSTRAINT asignacion_personal_pkey PRIMARY KEY (id);


--
-- Name: ayudantes ayudantes_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.ayudantes
    ADD CONSTRAINT ayudantes_pkey PRIMARY KEY (id);


--
-- Name: ayudantes ayudantes_telefono_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.ayudantes
    ADD CONSTRAINT ayudantes_telefono_unique UNIQUE (telefono);


--
-- Name: bienes bienes_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.bienes
    ADD CONSTRAINT bienes_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: choferes choferes_licencia_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.choferes
    ADD CONSTRAINT choferes_licencia_unique UNIQUE (licencia);


--
-- Name: choferes choferes_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.choferes
    ADD CONSTRAINT choferes_pkey PRIMARY KEY (id);


--
-- Name: choferes choferes_telefono_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.choferes
    ADD CONSTRAINT choferes_telefono_unique UNIQUE (telefono);


--
-- Name: clientes clientes_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_pkey PRIMARY KEY (id);


--
-- Name: clientes clientes_telefono_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_telefono_unique UNIQUE (telefono);


--
-- Name: configuracion_precios configuracion_precios_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.configuracion_precios
    ADD CONSTRAINT configuracion_precios_pkey PRIMARY KEY (id);


--
-- Name: configuracion_qr configuracion_qr_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.configuracion_qr
    ADD CONSTRAINT configuracion_qr_pkey PRIMARY KEY (id);


--
-- Name: deudas deudas_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.deudas
    ADD CONSTRAINT deudas_pkey PRIMARY KEY (id);


--
-- Name: dispositivos dispositivos_dispositivo_id_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.dispositivos
    ADD CONSTRAINT dispositivos_dispositivo_id_unique UNIQUE (dispositivo_id);


--
-- Name: dispositivos dispositivos_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.dispositivos
    ADD CONSTRAINT dispositivos_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: servicio_ayudante servicio_ayudante_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicio_ayudante
    ADD CONSTRAINT servicio_ayudante_pkey PRIMARY KEY (id);


--
-- Name: servicio_ayudante servicio_ayudante_servicio_id_ayudante_id_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicio_ayudante
    ADD CONSTRAINT servicio_ayudante_servicio_id_ayudante_id_unique UNIQUE (servicio_id, ayudante_id);


--
-- Name: servicios servicios_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicios
    ADD CONSTRAINT servicios_pkey PRIMARY KEY (id);


--
-- Name: servicios servicios_token_seguimiento_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicios
    ADD CONSTRAINT servicios_token_seguimiento_unique UNIQUE (token_seguimiento);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: ubicacion_gps ubicacion_gps_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.ubicacion_gps
    ADD CONSTRAINT ubicacion_gps_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: vehiculos vehiculos_pkey; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_pkey PRIMARY KEY (id);


--
-- Name: vehiculos vehiculos_placa_unique; Type: CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_placa_unique UNIQUE (placa);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: mudatrack_user
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: mudatrack_user
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: mudatrack_user
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: mudatrack_user
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: mudatrack_user
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: servicios_estado_fecha_servicio_index; Type: INDEX; Schema: public; Owner: mudatrack_user
--

CREATE INDEX servicios_estado_fecha_servicio_index ON public.servicios USING btree (estado, fecha_servicio);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: mudatrack_user
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: mudatrack_user
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: ubicacion_gps_servicio_id_fecha_hora_index; Type: INDEX; Schema: public; Owner: mudatrack_user
--

CREATE INDEX ubicacion_gps_servicio_id_fecha_hora_index ON public.ubicacion_gps USING btree (servicio_id, fecha_hora);


--
-- Name: bienes bienes_servicio_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.bienes
    ADD CONSTRAINT bienes_servicio_id_foreign FOREIGN KEY (servicio_id) REFERENCES public.servicios(id) ON DELETE CASCADE;


--
-- Name: choferes choferes_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.choferes
    ADD CONSTRAINT choferes_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: deudas deudas_cliente_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.deudas
    ADD CONSTRAINT deudas_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES public.clientes(id) ON DELETE CASCADE;


--
-- Name: deudas deudas_servicio_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.deudas
    ADD CONSTRAINT deudas_servicio_id_foreign FOREIGN KEY (servicio_id) REFERENCES public.servicios(id) ON DELETE CASCADE;


--
-- Name: dispositivos dispositivos_chofer_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.dispositivos
    ADD CONSTRAINT dispositivos_chofer_id_foreign FOREIGN KEY (chofer_id) REFERENCES public.choferes(id) ON DELETE CASCADE;


--
-- Name: dispositivos dispositivos_vehiculo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.dispositivos
    ADD CONSTRAINT dispositivos_vehiculo_id_foreign FOREIGN KEY (vehiculo_id) REFERENCES public.vehiculos(id) ON DELETE SET NULL;


--
-- Name: servicio_ayudante servicio_ayudante_ayudante_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicio_ayudante
    ADD CONSTRAINT servicio_ayudante_ayudante_id_foreign FOREIGN KEY (ayudante_id) REFERENCES public.ayudantes(id) ON DELETE CASCADE;


--
-- Name: servicio_ayudante servicio_ayudante_servicio_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicio_ayudante
    ADD CONSTRAINT servicio_ayudante_servicio_id_foreign FOREIGN KEY (servicio_id) REFERENCES public.servicios(id) ON DELETE CASCADE;


--
-- Name: servicios servicios_chofer_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicios
    ADD CONSTRAINT servicios_chofer_id_foreign FOREIGN KEY (chofer_id) REFERENCES public.choferes(id) ON DELETE SET NULL;


--
-- Name: servicios servicios_cliente_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicios
    ADD CONSTRAINT servicios_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES public.clientes(id) ON DELETE CASCADE;


--
-- Name: servicios servicios_vehiculo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.servicios
    ADD CONSTRAINT servicios_vehiculo_id_foreign FOREIGN KEY (vehiculo_id) REFERENCES public.vehiculos(id) ON DELETE SET NULL;


--
-- Name: ubicacion_gps ubicacion_gps_servicio_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: mudatrack_user
--

ALTER TABLE ONLY public.ubicacion_gps
    ADD CONSTRAINT ubicacion_gps_servicio_id_foreign FOREIGN KEY (servicio_id) REFERENCES public.servicios(id) ON DELETE CASCADE;


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: pg_database_owner
--

GRANT ALL ON SCHEMA public TO mudatrack_user;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO mudatrack_user;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO mudatrack_user;


--
-- PostgreSQL database dump complete
--

\unrestrict Ufv1mLDuDWPk80S5XcrG1ogrFd052wDuWlJEcwLLuTt16BOiMsd9wpkKPlk6nu3

