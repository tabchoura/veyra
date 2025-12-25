-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de table greenpul_veyra. cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage de la structure de table greenpul_veyra. cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage de la structure de table greenpul_veyra. failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage de la structure de table greenpul_veyra. jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage de la structure de table greenpul_veyra. job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage de la structure de table greenpul_veyra. migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table greenpul_veyra.migrations : ~5 rows (environ)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_12_01_214439_create_personal_access_tokens_table', 1),
	(5, '2025_12_01_221955_fix_password_reset_tokens_table', 2);

-- Listage de la structure de table greenpul_veyra. password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage de la structure de table greenpul_veyra. personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table greenpul_veyra.personal_access_tokens : ~25 rows (environ)
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 3, 'veyra-token', '7db8248487a5890d684b1d59e977729c2ffa88101c6731c8d83944fe95d25692', '["*"]', NULL, NULL, '2025-12-01 20:50:00', '2025-12-01 20:50:00'),
	(2, 'App\\Models\\User', 4, 'veyra-token', '7d66c5da59f28983d2e88eda9616e9617365c47723e9b74db64cf5be3c275ddf', '["*"]', NULL, NULL, '2025-12-01 20:50:38', '2025-12-01 20:50:38'),
	(3, 'App\\Models\\User', 5, 'veyra-admin-token', 'c77614ae0d0865741387ef50490d6d80779c7ea13966d906cb439abd197b64a7', '["*"]', '2025-12-01 21:02:57', NULL, '2025-12-01 21:02:41', '2025-12-01 21:02:57'),
	(4, 'App\\Models\\User', 4, 'veyra-token', '352fcaf00e834ea804d025065652a023f7947e5fb8da3f47d7c4ba5ce8338e30', '["*"]', NULL, NULL, '2025-12-01 21:05:25', '2025-12-01 21:05:25'),
	(5, 'App\\Models\\User', 4, 'veyra-token', 'af620615db65b0798b81f69343ab55f6b280588623a45667ffe4d8d8423d92b7', '["*"]', NULL, NULL, '2025-12-01 21:35:41', '2025-12-01 21:35:41'),
	(6, 'App\\Models\\User', 4, 'veyra-token', 'e71810fde6213990a789cd7008dc005f9f66479af4f30016b3c5ea7d7d00a2d0', '["*"]', NULL, NULL, '2025-12-01 21:36:35', '2025-12-01 21:36:35'),
	(7, 'App\\Models\\User', 5, 'veyra-admin-token', '593dd6e54e17c0856b915c05aa93a71c9f49f1fdb3a88acb940eb3c98514615d', '["*"]', '2025-12-01 21:36:55', NULL, '2025-12-01 21:36:53', '2025-12-01 21:36:55'),
	(8, 'App\\Models\\User', 5, 'veyra-admin-token', '404dd9b64eac720ca9900c7904f0f2a4916d721c7870be0bd6b3919e131bb87d', '["*"]', '2025-12-01 21:39:06', NULL, '2025-12-01 21:39:05', '2025-12-01 21:39:06'),
	(9, 'App\\Models\\User', 5, 'veyra-admin-token', 'c86e61e783d40a9933ba264f6ac8d1ad522645f1613f818b3324540d7520b6b1', '["*"]', '2025-12-01 21:43:19', NULL, '2025-12-01 21:43:14', '2025-12-01 21:43:19'),
	(10, 'App\\Models\\User', 5, 'veyra-admin-token', '49d3af6194403850fb507fee7d5218dcbeabafacfd576d41675088a758e2ddf9', '["*"]', '2025-12-02 18:29:30', NULL, '2025-12-02 18:29:28', '2025-12-02 18:29:30'),
	(11, 'App\\Models\\User', 5, 'veyra-admin-token', '47406413dce7daa6a90abe4e79666fcf624bc3d2033bb1c939e6b8561a321450', '["*"]', '2025-12-20 21:53:33', NULL, '2025-12-20 21:53:31', '2025-12-20 21:53:33'),
	(12, 'App\\Models\\User', 6, 'veyra-token', '1ea1803cdd9e18fbb35c23baa054935589e9d7700b81465286bb5e6ab698a208', '["*"]', NULL, NULL, '2025-12-21 21:21:41', '2025-12-21 21:21:41'),
	(13, 'App\\Models\\User', 7, 'veyra-token', '6a25e819954ea95c43ed3b517470d050fd8581250e48aff0cff031be4d732c2b', '["*"]', NULL, NULL, '2025-12-21 21:22:00', '2025-12-21 21:22:00'),
	(14, 'App\\Models\\User', 5, 'veyra-admin-token', 'b470b1cfd5f257cbb954c8f9f720e4dea454623adbfedba9f2193965d90e3a59', '["*"]', NULL, NULL, '2025-12-21 21:22:47', '2025-12-21 21:22:47'),
	(15, 'App\\Models\\User', 5, 'veyra-admin-token', 'bda63496498839d8f47907d7bc9ebd9be51d9a6c754b0562c111e82334a599b0', '["*"]', NULL, NULL, '2025-12-21 21:27:51', '2025-12-21 21:27:51'),
	(16, 'App\\Models\\User', 5, 'veyra-admin-token', 'd6a755c3433fdfdc4f4817a451dbf9f1c2b5ea808df7c10505b54235933eb895', '["*"]', NULL, NULL, '2025-12-21 21:32:09', '2025-12-21 21:32:09'),
	(17, 'App\\Models\\User', 5, 'veyra-admin-token', '14d647e4cfad0fcb574635b82ab1895cd6e73d5b7d46da3ad444802476d72f46', '["*"]', '2025-12-21 21:33:48', NULL, '2025-12-21 21:33:24', '2025-12-21 21:33:48'),
	(18, 'App\\Models\\User', 8, 'veyra-token', 'a9e2aaf6420dfa89f24b33cbb1cac3f74d11fb01a3d8dab40179c5afff0c3ae1', '["*"]', NULL, NULL, '2025-12-21 21:41:30', '2025-12-21 21:41:30'),
	(19, 'App\\Models\\User', 9, 'veyra-token', 'd0d1987d6ebfa82f95c88a75325ef7179bb696744c6124063970eca9cd382e1a', '["*"]', NULL, NULL, '2025-12-21 22:20:22', '2025-12-21 22:20:22'),
	(20, 'App\\Models\\User', 5, 'veyra-admin-token', 'a9036e363721fdf1fe010c59d7edf1fc428a1eb3d54f42a8c59139d407536b39', '["*"]', '2025-12-21 22:21:12', NULL, '2025-12-21 22:21:08', '2025-12-21 22:21:12'),
	(21, 'App\\Models\\User', 9, 'veyra-token', 'bc2d39016c2b5bc3805a63e08c63d2d0aebf4d8b12efc097ee83684b6a79fb75', '["*"]', NULL, NULL, '2025-12-21 22:23:14', '2025-12-21 22:23:14'),
	(22, 'App\\Models\\User', 10, 'veyra-token', 'd2efb3ea947ff6c68b360e10774a880662d0f0a2b676d15ae73a27b555d90736', '["*"]', NULL, NULL, '2025-12-21 23:13:00', '2025-12-21 23:13:00'),
	(23, 'App\\Models\\User', 5, 'veyra-admin-token', '271c0ed0fc503f780787c5ec2e55f4e09f5f0c90c57f38c0c82c4fae867632dd', '["*"]', '2025-12-21 23:24:13', NULL, '2025-12-21 23:14:19', '2025-12-21 23:24:13'),
	(24, 'App\\Models\\User', 5, 'veyra-admin-token', 'cd45e0ac6392071021d7647ca1beba86eb613ea5bea20048f431dcc9d3fb6f2d', '["*"]', '2025-12-21 23:42:18', NULL, '2025-12-21 23:42:17', '2025-12-21 23:42:18'),
	(25, 'App\\Models\\User', 5, 'veyra-admin-token', '5bbf9f5a8ed716ba06f5bf804b1e2f231644865536e310de6a8d5819dedbf9b6', '["*"]', '2025-12-21 23:54:44', NULL, '2025-12-21 23:54:43', '2025-12-21 23:54:44');

-- Listage de la structure de table greenpul_veyra. sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage de la structure de table greenpul_veyra. users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `email_verification_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tva_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `function` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sector` enum('Textile & mode','Agroalimentaire','Électronique & technologies','Construction & BTP','Industrie manufacturière','Énergie (production, distribution, stockage)','Immobilier & gestion d''actifs','Chimie, plasturgie & matériaux','Emballage & logistique','Automobile & mobilité','Autres') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sector_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `partner` enum('bAwear Score','FTTH','MODINT','CBI','Autre') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `partner_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` enum('fr','en') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fr',
  `ip_signup` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signup_at` timestamp NULL DEFAULT NULL,
  `user_type` enum('ADMIN','USER','SUPER_USER') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SUPER_USER',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `dpp_access` tinyint unsigned NOT NULL DEFAULT '0',
  `quota_dpp` int NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table greenpul_veyra.users : ~9 rows (environ)
INSERT INTO `users` (`id`, `email`, `password`, `email_verified`, `email_verification_token`, `company_name`, `logo_url`, `tva_number`, `first_name`, `last_name`, `function`, `address1`, `address2`, `postal_code`, `country`, `sector`, `sector_other`, `partner`, `partner_other`, `language`, `ip_signup`, `signup_at`, `user_type`, `status`, `dpp_access`, `quota_dpp`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'votre-email@exemple.com', '$2y$12$v882u4rFXtYZiS7dPh8xUeSy/ph/WKUZO5hOsjDYXLFMJMgCxyzDu', 1, NULL, 'Test Company', NULL, 'TVA123456', 'John', 'Doe', NULL, '123 Test Street', NULL, '1000', 'Belgique', 'Autres', NULL, NULL, NULL, 'fr', NULL, NULL, 'SUPER_USER', 'rejected', 0, 0, NULL, '2025-12-01 20:46:17', '2025-12-01 21:02:51'),
	(3, 'mariiembchir@gmail.com2', '$2y$12$tf8SrVbtK2dwkkR5mElSkuzv5RKzEU0t/mGDTjAyWqgA/iX7PB8oS', 0, 'SbrieOh4xJXSloIaOkmfudwxKFamoAWCZAtaiDVs8NweV9UftGi6N6dL9q6PTTQV', 'k', 'logos/lPznIRPss6ga6P6LNTR1IRXRaV6K4x58pfFbqPQT.jpg', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'Albania', 'Agroalimentaire', NULL, 'bAwear Score', NULL, 'fr', '127.0.0.1', '2025-12-01 20:49:55', 'SUPER_USER', 'rejected', 0, 0, NULL, '2025-12-01 20:49:55', '2025-12-01 21:02:54'),
	(4, 'mariiembchir@gmail.com', '$2y$12$Ut.T1t9RkmjEh2XvebrcGewoK4FLUGiGO98EGJdmRi4MgD19TuW0a', 1, 'dpdjosukFsLTiX999YdbQUWPMPougqJgYC00GTtx6dToAPs1TjknrM5PauIsm3Jh', '7', NULL, 'z', 'z', 'z', 'z', 'z', 'z', 'z', 'Afghanistan', 'Textile & mode', NULL, 'bAwear Score', NULL, 'fr', '127.0.0.1', '2025-12-01 20:50:34', 'SUPER_USER', 'rejected', 0, 0, NULL, '2025-12-01 20:50:34', '2025-12-01 21:43:19'),
	(5, 'admin@veyra.com', '$2y$12$9vNsclcGEoKYSGsnN6OGDe4EL9dqnWY7DLYUe1uIVlsJGMwEk0i36', 1, NULL, 'Veyra Admin', NULL, 'ADMIN-TVA', 'Admin', 'Veyra', 'Super Admin', 'Adresse admin', NULL, '00000', 'France', 'Autres', NULL, NULL, NULL, 'fr', NULL, NULL, 'ADMIN', 'approved', 2, 0, NULL, '2025-12-01 21:02:23', '2025-12-01 21:02:23'),
	(6, 'test+1@veyra.com', '$2y$12$/Hk2ot0lW586WkUWxhs3We.r0T8jI9ocdUdcZ93xcd0YXgyonn0ky', 0, '7VFfG8Niv1EYkheRmYXwIagQr0ZcJjyQBCI3M2SSFRDGcKZzJj2vELWxmDggRzDW', 'Veyra Test', NULL, 'FR123456789', 'Lina', 'Merchaoui', 'CEO', '10 Rue de la République', 'Bâtiment B', '75001', 'France', 'Textile & mode', NULL, 'bAwear Score', NULL, 'fr', '127.0.0.1', '2025-12-21 21:21:37', 'SUPER_USER', 'pending', 0, 0, NULL, '2025-12-21 21:21:38', '2025-12-21 21:21:38'),
	(7, 'mariem.bchir@polytechnicien.tn', '$2y$12$/9ElbggLNvvAEseL.PudwuOQRriLcRjE7E9R3Jfg1Uz.XDuLzI9VW', 1, 'mjNmej7RvELqLBEGlk86xuS5O7dH4mKsFJof6FGV0SluZQOc1CcNXuTuLX8HMEyN', 'Veyra Test', NULL, 'FR123456789', 'Lina', 'Merchaoui', 'CEO', '10 Rue de la République', 'Bâtiment B', '75001', 'France', 'Textile & mode', NULL, 'bAwear Score', NULL, 'fr', '127.0.0.1', '2025-12-21 21:21:59', 'SUPER_USER', 'approved', 0, 0, NULL, '2025-12-21 21:21:59', '2025-12-21 21:33:48'),
	(8, 'bchirmariiem@gmail.com', '$2y$12$GxtVh47AKzhkfVoyIka3JuNfLNOcwI23tEhDmyMKi8Pu/c1k0eMlK', 0, 'yUVn4Z4dhuXtXco8Id6t5kM53Wg3oG0iYS60IjMTz43iIjh1igO58JlVVT94u5cS', 'aa', 'logos/mtGNmYHNnemkSS5tlnQLYr2htmisrGzLRd25VKdf.jpg', 'aa', 'aa', 'aa', 'a', 'aa', 'a', 'a', 'Afghanistan', 'Textile & mode', NULL, 'bAwear Score', NULL, 'fr', '127.0.0.1', '2025-12-21 21:41:28', 'SUPER_USER', 'pending', 0, 0, NULL, '2025-12-21 21:41:29', '2025-12-21 21:41:29'),
	(9, 'sklo.romo@yahoo.fr', '$2y$12$k1EoLnzqH69Y4VaN3Fyz7.9GHl5jVxHEhy3PCxU/li.8hVUw/vC7a', 1, 'zHCw7aeQxX8TdzACNNtGWvDsxd1a30gHC47iryOTOIru5XA60gJBSBBiX4kPvpO5', 'd', 'logos/IcPIiitkSR415h9mmvAjVjvCIL3svWhVDuWrMlLI.jpg', 's', 'z', 'z', NULL, 'q', 'q', '5', 'Afghanistan', 'Textile & mode', NULL, 'bAwear Score', NULL, 'fr', '127.0.0.1', '2025-12-21 22:20:19', 'SUPER_USER', 'approved', 0, 0, NULL, '2025-12-21 22:20:19', '2025-12-21 22:21:12'),
	(10, 'rami.sakly@etudiant-enit.utm.tn', '$2y$12$2vyDWME55wMexHvZfPcdHuZRC5rcvkoXfR4wxeSHuKojOgaEK8oB2', 1, '26e0aljEMsFybRbeUZYMYikY7yMnKd7U6arcF8k0xRpAtovVmj2ghNXUFPO3wrso', 'rr', NULL, '888', 'sakli', 'rami', 'a', 'eaaa8', 'z', '8855', 'Afghanistan', 'Textile & mode', NULL, NULL, NULL, 'fr', '127.0.0.1', '2025-12-21 23:12:55', 'SUPER_USER', 'approved', 0, 0, NULL, '2025-12-21 23:12:56', '2025-12-21 23:14:31');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
