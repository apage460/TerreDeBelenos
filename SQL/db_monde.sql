-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: heckbert.iad1-mysql-e2-1b.dreamhost.com
-- Generation Time: Jan 09, 2025 at 02:00 PM
-- Server version: 8.0.28-0ubuntu0.20.04.3
-- PHP Version: 8.1.2-1ubuntu2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_monde`
--
CREATE DATABASE IF NOT EXISTS `db_monde` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_monde`;

-- --------------------------------------------------------

--
-- Table structure for table `baronnies`
--

CREATE TABLE `baronnies` (
  `Id` int NOT NULL,
  `Cadastre` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `Nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `IdComte` int NOT NULL,
  `Baron` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Niveau` int NOT NULL DEFAULT '0',
  `Specialisation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Developpement` longtext COLLATE utf8mb4_general_ci,
  `CodeEtat` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `Assignable` int NOT NULL DEFAULT '0',
  `IdPersonnage` int DEFAULT NULL,
  `DateAnobli` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baronnies`
--

INSERT INTO `baronnies` (`Id`, `Cadastre`, `Nom`, `IdComte`, `Baron`, `Niveau`, `Specialisation`, `Developpement`, `CodeEtat`, `Assignable`, `IdPersonnage`, `DateAnobli`) VALUES
(1, '104', 'Yllir\'iness', 27, 'Aucun - Elfe sauvage recherché', 1, NULL, 'Futur siège comtal d\'Alenia Yana Trradi, la comtesse cherche présentement un seigneur pour diriger les efforts de construction.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(2, '109', 'Terre vierge', 27, 'Aucun - Elfe sauvage recherché', 0, NULL, 'Future baronnie elfique en devenir, cette terre a été donnée au peuple sylvestre pour les accueillir.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(3, '110', 'Terre vierge', 27, 'Aucun - Elfe sauvage recherché', 0, NULL, 'Future baronnie elfique en devenir, cette terre a été donnée au peuple sylvestre pour les accueillir.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(4, '105', 'Eral\'drinn', 27, 'Randall Greiÿle Vanderson, fils de Louve', 2, NULL, 'Cité sylvestre en construction, Eral\'drinn se veut à la fois le futur centre du comté de Cendreterre et une mise à l\'épreuve des capacités de la nouvelle génération de lycans à faire commerce et diplomatie. En attendant la construction du siège comtale plus au nord, la baronnie sert également de capitale temporaire.', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(5, '106', 'Terres-Brûlées', 27, 'Zo-Ko ', 0, 'Herboristerie', 'Terre dévastée par Dagoth dans sa campagne visant à détuire le Faux-Prophète de Gaea, cette baronnie a aujourd\'hui pris les allures d\'un bois marécageux où de grandes parts du sol sont constamment inondées. Cet environnement humide convient en échange parfaitement à son seigneur et, même s\'il ne permet pour l\'instant pas de grands développement, permet une culture exceptionnelle de plantes diverses.', 'ACTIF', 0, 1965, '2023-12-29 12:56:41'),
(6, '107', 'Terre vierge', 3, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(7, '108', 'Terre vierge', 3, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(8, '112', 'Boisé-Profond', 3, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(9, '111', 'Chesne', 3, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(10, '117', 'Dioltas', 5, 'Erika Mac Aonghusa', 3, 'Culture et Alcool', 'Associée à la Redoute par un pacte commercial, les habitants de cette terre visent la propagation de la culture et la production d\'alcools uniques. Contrée accueillante, il s\'agit également d\'une terre d\'accueil pour les démonistes qui cherchent la paix.', 'ACTIF', 0, 5878, '2023-12-29 12:56:41'),
(11, '124', 'Terre vierge', 5, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(12, '128', 'Le Havre', 5, 'Maël Stromff, Capitaine Aube', 1, 'Alcool', 'L\'une des fabriques d\'alcool les plus réputées du Trône, Le Havre appartient à l\'organisation connue sous le nom du Bazar et est responsable de la distribution du tiers de l\'alcool que l\'on trouve en Francourt.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(13, '129', 'Saint-Owen', 5, 'Maître Léonard Pélerin', 4, 'Centre Ademos', 'Terre fertile située juste au nord d\'Hyden, Saint-Owen accueille un communauté grandissante d\'Adémos au sein du Trône de l\'Est. Préférant un style de vie moins citadin, les habitants ont développé la baronnie en étalant davantage ses bâtiments en un grand réseau de villas et refuges plutôt qu\'en un seul grand bourg. ', 'ACTIF', 0, 888, '2023-12-29 12:56:41'),
(14, '137', 'Vertecolline', 9, 'Aucun', 0, NULL, NULL, 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(15, '125', 'Coeur-de-Jet', 5, 'Capitaine Basil St-Germain', 2, 'Armada', 'L\'un des grands ports du Trône de l\'Est et le siège de sa flotte, la baronnie n\'accueille en échange pas une grande population et, outre les établissements de gouvernance, les chantiers navals présents sont entourés en grande partie de tavernes, de commerces essentielles à la vie de tous les jours et des habitations des travailleurs.<br/>\n<br/>\nNonobstant sa cruelle simplicité, la ville attire bien des marins et leur capitaine, qui privilégie ce port d\'attache aux autres amarrages du fleuve Rahcal. De par ce fait, la baronnie jouit d\'une certaine réputation et ses habitants ne manquent jamais d\'opportunités.', 'ACTIF', 0, 6273, '2023-12-29 12:56:41'),
(16, '115', 'Falswick', 29, 'Gaston Salazar', 3, 'Défenses', 'Ligne fortifiée frôlant la démesure, la baronnie accueille principalement la grande forteresse servant de base d\'opération aux Faucons de Falswick, ainsi que toutes les défenses requises pour créer une division claire entre le nord de Champagnol et les forêts de Taurë Ilfirin, incluant une majestueuse muraille de pierre entièrement financée par le seigneur de l\'endroit, Gaston Salazar.<br/>\n<br/>\nIl va sans dire que le paysage de Falswick diffère grandement du reste des terres agraires de Champagnol et qu\'à l\'exception de Chastel-Blanc, il n\'existe aucune fortification dans le pays pouvant rivaliser avec celui que l\'on trouve ici.', 'ACTIF', 0, 6315, '2023-12-29 12:56:41'),
(17, '122', 'Ormesson', 29, 'Julien Prisk', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(18, '116', 'Motte-Palustre', 29, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(19, '123', 'Terre vierge', 29, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(20, '126', 'Terre vierge', 36, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(21, '130', 'Pervenche', 36, 'Saito Salazar', 1, 'Alchimie', 'Un joli petit hameau d\'apparence paisible entourant l\'une des plus grandes institutions académiques de Champagnol. Dédiée plus spécialement à l\'étude de l\'alchimie et de l\'ingénierie arcanique, l\'Institut de recherche et d\'excellence de Saïto Salazar est volontairement située loin des plus grandes bourgades. Le cursus enseigné et les recherches qu\'on y mènent touchant des sujets plutôt explosifs, joint au fait que l\'institution fait délibéremment fit de plusieurs mesures de sécurité vues comme obligatoires ailleurs à Bélénos (au nom du progrès bien sûr), le seigneur des terre a pris cette mesure afin d\'éviter un grand nombre de plaintes. Loin d\'être sans coeur ou lâche cependant, un grand dispensaire de soins a été construit par le renommé soigneur Hylo de Kolnick afin de voir à la gestion de tout accident. ', 'ACTIF', 1, 6209, '2023-12-29 12:56:41'),
(22, '133', 'Terre vierge', 36, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(23, '127', 'Pré-Cieux', 7, 'Robert IV de Champagnol', 2, NULL, 'L\'un des grands villages de Champagnol, Pré-Cieux possède la chance d\'être mieux fortifié et mieux éduqué que la moyenne des bourgades du pays. <br/>\n<br/>\nLieu de naissance de Robert IV de Champagnol, il assure personnellement son développement et sa gouvernance. Il est aujourd\'hui confirmé que celui-ci s\'y retirera lors de la passation du pouvoir au prochain régent.', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(24, '131', 'Cham-Paître', 7, 'Arthur des Grands-Moulins', 0, NULL, 'Terre largement occupée par la culture, le seul bâtiment notoire que l\'on y trouve est un orphelinat où la majorité des enfants champagnolais sans parent sont envoyés.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(25, '134', 'Mont-aux-Fleurs', 36, 'Erbro Urcel', 1, 'Métal', 'Mines et petit village montagnard.', 'ACTIF', 1, 4927, '2023-12-29 12:56:41'),
(26, '136', 'Hâvre-aux-Mulots', 36, 'Lisalie Després', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(27, '151', 'Franc-Moisson', 7, 'Rosaire Belivoski', 0, NULL, 'Anciennement la terre du seigneur Tayeverre, celle-ci était autrefois spécialisée dans la fabrique d\'armement et d\'objets d\'art liés à la guerre, avant que la guerre, les pillage et sa situation éloignée de Chastel-Blanc ne mène à son abandon.<br/>\n<br/>\nAujourd\'hui, tout est à reconstruire, et la production d\'armes de Champagnol ayant été relocalisée plus près de la cité et des grands villages, la chose ne sera pas facile. ', 'ACTIF', 1, 7044, '2023-12-29 12:56:41'),
(28, '135', 'Bergenheim', 7, 'Brynhild Thurska Birger Bergensdottìr', 2, NULL, 'Terre offerte par Robert V de Champagnol aux réfugiés de Terre-des-Brumes en échange de leurs services et de leur aide pour défendre le royaume, Bergenheim, nommé en l\'honneur du père de la baronne, a su se développer avec le temps pour devenir un grand village où les milices champagnolaises sont envoyées afin de recevoir des entraînements supplémentaires et particuliers.<br/>\n<br/>\nSeuls ceux qui obtiennent l\'approbation du Clan maître de l\'endroit peuvent s\'y établir de manière permanente. La tradition spirite de Terre-des-Brumes y est gardée vivante et le Clan règnant y prie les esprits d\'Andrumìr, Sigstein, Sigvalda, Flika, Soren et Ulfrik. Les Galléonites y sont proscrits vu la situation avec les Nordiens.', 'ACTIF', 0, 8337, '2023-12-29 12:56:41'),
(29, '132', 'Chastel-blanc', 7, 'Robert V de Champagnol', 10, NULL, 'Capitale champagnolaise, Chastel-Blanc est l\'une des cités ayant survécu à la fin des Grands Duchés. Il s\'agit ainsi de l\'une des trois « Cités blanches » appartenant à un temps révolu.<br/>\n<br/>\nOutre son aspect historique, Chastel-Blanc est la seul cité possédant une muraille de pierre de Champagnol. La seule autre fortification du pays possédant ce type de défenses est la Ligne Falswick au nord. En tant que tel, la cité attire la quasi-totalité du commerce du royaume et abrite la plus grande part des étrangers venus au pays. Il s\'agit d\'un lieu d\'ordre et de pays, possédant une force régulière plutôt qu\'une milice, et particulièrement bien surveillé par la régence.', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(30, '118', 'De la Croix', 1, 'James II Beausoleil', 0, NULL, NULL, 'ACTIF', 0, 8821, '2023-12-29 12:56:41'),
(31, '119', 'Cité de Solèce', 1, 'Nathaniel Everlin', 10, 'Reliques', 'Lieu plus que riche en histoire et en guerre, la Cité sainte de l\'Aédon abrite le très sacré Chesne d\'Usire, un chêne rouge gigantesque que même le grand Dagoth n\'a su approcher. Il va sans dire qu\'il s\'agit d\'un lieu d\'une importance indescriptible pour les Aédonites, particulièrement pour ceux de l\'Empire à l\'ouest. Il s\'agit également de l\'une des trois « Cités blanches » du passé.<br/>\n<br/>\nSouvent le théâtre de grande bataille, elle fut conquise par Dagoth suite à la Guerre des Faux-Prophète et divisée entre déistes et démonistes. Vivant donc sur de fragiles ententes forcées, la paix est constamment menacée par la pression entre les deux camps. L\'Ordre de Sainte-Valérie s\'assure que cette paix n\'éclate pas en morceaux, mais son influence et sa patience a des limites.\n\nRécemment reconquise par les Aédonites avec la bénédiction de la Reine Blanche, la Cité entre dans une nouvelle ère.', 'ACTIF', 0, 2997, '2024-08-13 18:48:33'),
(32, '113', 'De la Rose', 1, 'Margaret De la Rose', 0, NULL, NULL, 'ACTIF', 0, 7759, '2023-12-29 12:56:41'),
(33, '114', 'Terre vierge', 2, 'Raphaël Bertolini', 0, NULL, 'Terre fertile avec quelques fermes.', 'ACTIF', 1, 1903, '2023-12-29 12:56:41'),
(34, '121', 'Grèvienne', 2, 'Arthurus Rohanov', 0, NULL, NULL, 'ACTIF', 1, 3249, '2023-12-29 12:56:41'),
(35, '120', 'La Redoute', 2, 'Thorbjorn Sigurdsson', 8, 'Métaux précieux', 'Cité-forteresse autrefois connue sous le nom de « Redoute Marniet », la ville a vécu un déclin après la chite du Fief du même nom. Elle se tourne aujourd\'hui vers le commerce, tentant de devenir l\'un des grands ports du royaume et de se démarquer dans le commerce des métaux précieux.', 'ACTIF', 0, 4956, '2023-12-29 12:56:41'),
(36, '309', 'Nouvelle-Blivek', 26, 'Oscar Ferdinant', 1, 'Ville d\'accueil', 'Présentement modeste, cette nouvelle baronnie cherche à accueillir d\'abord les Aykanites exilés de Blivek, mais également tout déiste en besoin d\'un nouveau chez-lui, exception faite des Galléonites. À moyen terme, la baronnie cherche à se transformer en havre militarisé capable de soutenir n\'importe quel assaut de Trône de l\'Est.', 'ACTIF', 0, 6856, '2023-12-29 12:56:41'),
(37, '308', 'Terre vierge', 26, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(38, '307', 'Nouvelle-Espérance', 26, 'Aucun', 4, NULL, 'Siège fortifié des Aykanites au nord de Bélénos et à la frontière entre l\'Empire et les Nordiens, Nouvelle-Espérance est présentement à la recherche d\'un baron qui puisse alléger la tâche de la comtesse dans la gestion de sa terre. On dit cependant que celle-ci se montre exigente face aux qualités requises pour devenir seigneur du joyau qu\'elle a bâti.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(39, '100', 'Cité d\'Halvard', 4, 'Allix Eikugoronoblashiverol', 8, NULL, 'Grande cité cosmopolite au centre du pays, Halvard est plus accueillante pour les étrangers que les autres villes ilfirinoises. Par conséquent, elle possède la seconde université du royaume et est souvent l\'hôte des grandes rencontres avec les autres peuples. <br/>\n<br/>\nEn Taurë Ilfirin, cela fait d\'elle la « capitale du commerce ».', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(40, '102', 'Terre vierge', 4, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(41, '103', 'Tirith Orn\'', 4, 'Vallério Astinius', 3, NULL, 'Ville collégiale en développement, Tirith Orn\' jongle présentement avec le projet d\'une nouvelle route commerciale liant l\'est et l\'ouest du royaume, ainsi qu\'avec des recherches sur Voronwë, dit le Ciseleur d\'Argent. Il s\'agit également de la baronnie du second archon de Taurë Ilfirin, Vallério Astinius. ', 'ACTIF', 0, 4460, '2023-12-29 12:56:41'),
(42, '101', 'Neldeyularyón', 4, 'Amarilysse Delanor', 1, NULL, 'Baronnie soeur de Tirith Orn\', elle est l\'instigatrice du projet pour une nouvelle route commerciale d\'est en ouest dans le royaume. Sa seigneur se fait également l\'avocate d\'une paix entre Taurë Ilfirin et Champagnol, et ce malgré les plans du roi.', 'ACTIF', 0, 5244, '2023-12-29 12:56:41'),
(43, '300', 'Marais de Feu', 30, 'Loxo', 0, NULL, 'Un marais presque magique, excessivement dangereux pour celui qui ne sait pas le naviguer, et au centre duquel se trouve la flamme que le roi Nostrum créa afin de tempérer son royaume selon ses désirs.<br/>\n<br/>\nLes Gardiens du marais sont des créatures et des druides féroces qui ne laissent aucun intrus s\'écarter de la seule route qui traverse cette terre.', 'ACTIF', 0, 396, '2023-12-29 12:56:41'),
(44, '301', 'Bosquet de la Louve', 30, 'Noélie', 1, NULL, 'Parsemée d\'antres éparses et de petits territoires de chasses, il s\'agit de la première terre appartenant aux Lycans de Taurë Ilfirin. Plus féroces, mais aussi plus sauvages que la « nouvelle génération », le roi a donc limité leurs mouvements à l\'intérieur de son royaume. En échange, il leur fit donc de cette terre, qui constitue le coeur de leur communauté.', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(45, '303', 'Ainas Ilfirin', 30, 'Nostrum', 0, NULL, 'Bosquet sacré de Gaea, il s\'agit de la retraite personnelle du roi Nostrum et le lieu où il s\'éclipse lorsqu\'il revet le manteau d\'Avatar.', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(46, '305', 'Pendrath Tiren', 30, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(47, '306', 'Delhenwë', 31, 'Aldaron Voronwë', 3, NULL, 'Terre valorisant le savoir et les coutumes de la Cour, elle attire particulièrement la noblesse elfique.', 'ACTIF', 0, 1346, '2023-12-29 12:56:41'),
(48, '307_1', 'L’Égide', 31, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(49, '304', 'Cité d’Hérove', 31, 'Sigmar L. Priam', 10, NULL, 'Capitale du pays, la cité est divisée entre les influences elfiques nobles et celle des Aykanites auxquels le roi promis la terre en échange du sacrifice du Talion. À ce jour, les Elfes possède davantage d\'influence dans la cité elle-même, alors que le Talion possède un plus grand contrôle des terres environnantes, créant un équilibre obligé dans leurs relations, mais nourissant également la division entre les peuples. <br/>\n<br/>\nHérove est également le lieu où l\'on trouve la plus grande université ilfirinoise, réputée pour garder jalousement les savoirs inédits appartenant aux Elfes.', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(50, '302', 'Bereg Reki', 31, 'Aucun', 0, 'Commerce', 'Terre connectant Hérove et les Marais de feu, elle accueille quelques haltes de commerce sur la Route du Nord et rien de plus notoire.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(51, 'eseldorf', 'Cité d\'Eseldorf', 32, 'Reinhart', 10, 'Cristaux', 'Aussi appelée la « Cité grise » ou encore la « Cité de l\'Ordre », Eseldorf est une cité fermée où très peu de gens peuvent entrer et où ceux qui en sortent sont mandaté par le Guide suprême pour exécuté une mission et sont destinés à ne jamais revenir. Vouant un culte presque fanatique aux Fils et Filles de Mador et à leur Créatrice, les corps expéditionnaires exilés par la ville afin de promouvoir l\'Ordre sur les terres de Bélénos sont réputés pour leur ferveur légendaire et leur dévouement à leur cause, n\'acceptant aucun compromis dans leur mission.<br/>\n<br/>\nRécemment cependant, la cité a commencé à accepter de s\'ouvrir quelque peu au monde extérieur et a créé une zone commerciale à proximité de ses grandes portes. Quelques élu servent d\'intermédiaires avec ce marché. Ils restent les seuls à pouvoir entrer et sortir librement de la cité.', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(52, '1', 'Chateauvieu', 20, 'Victoire Chateauvieu', 2, NULL, 'L\'une des plus vieille baronnie de l\'Aurélius et l\'une des plus influentes, datant des Grands Duchés et servant de patrie à l\'une des plus anciennes familles nobles du pays. Le Manoir Chateauvieu est l\'une des merveilles de ce pays pour ceux qui apprécient l\'histoire.', 'ACTIF', 0, 62, '2023-12-29 12:56:41'),
(53, '2', 'Terre vierge', 20, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(54, '3', 'Terre vierge', 20, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(55, '4', 'Terre vierge', 20, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(56, '5', 'Terre vierge', 20, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(57, '6', 'Fort Drake', 20, 'Famille Durance', 2, NULL, 'Fort', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(58, '7', 'Terre vierge', 20, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(59, '8', 'Asterling', 20, 'Aucun', 2, NULL, 'Capitale de Maillence et lieu de résidence de la marquise, Asterling est un lieu de réunion important pour la faction aédonite du pays.<br/>\n<br/>\nIsabelle de Méricourt étant chevalière d\'origine, Asterling possède également les barraquements d\'une bonne part des forces du duché.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(60, '17', 'Gué-de-Maillence', 20, 'Aucun', 3, NULL, 'Cité gardant la traversée du fleuve et servant de port principal pour le duché.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(61, '16', 'Cité de la Bastide', 22, 'Aucun', 6, NULL, 'Cité forte', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(62, '15', 'Gué-du-Loup', 22, 'Aucun', 1, NULL, 'Hameau', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(63, '18', 'Terre vierge', 22, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(64, '23', 'Terre vierge', 21, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(65, '19', 'Quatre Étoiles', 21, 'Madeleine Astral de Brandebourg', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(66, '12', 'Cité d\'Ardast', 21, 'Florence Dubrouillard', 8, NULL, 'Cité universitaire', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(67, '9', 'Terre vierge', 18, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(68, '10', 'Ville de Hautlangeois', 18, 'Aucun', 6, NULL, 'Cité forte', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(69, '11', 'Roy', 18, 'Aucun', 4, NULL, 'Bourg', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(70, '13', 'Cité d\'Héodim', 17, 'Dagon d\'Héodim', 10, NULL, 'Capitale', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(71, '14', 'Haut-Jardins', 17, 'Aucun', 2, NULL, 'Village', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(72, '22', 'Terre vierge', 17, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(73, '21', 'Asagao-Ken', 17, 'Morina', 2, 'Jardins fauniques', 'Dirigée par le clan Takeda, cette terre abrite un petit village aux accents de Shataï, ainsi qu\'une grande réserve faunique divisée en \"jardins\" servant de refuge à une grande variété d\'animaux tant communs qu\'exotiques. Ces jardins sont utilisés par les invités du clan pour relaxer ou encore à des fins académiques.', 'ACTIF', 0, 8835, '2023-12-29 12:56:41'),
(74, '20', 'L\'Ordre du Zénith', 17, 'Aucun', 2, NULL, 'Temple fort', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(75, '24', 'Terre vierge', 17, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(76, '25', 'Terre vierge', 17, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(77, '26', 'Lune d\'Argent', 17, 'Ferdinand du Château', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(78, '27', 'Ville de Vertalia', 19, 'Aucun', 6, NULL, 'Cité', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(79, '28', 'Aubran', 19, 'Fergus Makelroy', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(80, '32', 'Terre vierge', 19, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(81, '36', 'Haureville', 19, 'Alexandru Brostov', 2, NULL, 'Village fortifié', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(82, '141', 'Marché Nicolet', 9, 'Aucun', 5, NULL, NULL, 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(83, '138', 'Pistaguer', 9, 'Aucun', 10, NULL, 'Métropole', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(84, '139', 'SuaÏo', 9, 'Aucun', 2, NULL, 'Fort', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(85, '140', 'Jaspérie', 19, 'Hector Vieux-Vin', 4, 'Cité indépendante', 'Ville fortifiée ayant autrefois fait partie du comté aurélois de Boursicot, la ville fut la seule à tenir bon face aux assauts des Croisés. À la fin de la guerre, le Trône et l\'Aurélius s\'entendirent pour faire de la ville un lieu de rencontre et de paix.', 'ACTIF', 0, 691, '2023-12-29 12:56:41'),
(86, '41', 'Berge-Longue', 23, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(87, '43', 'Novanadej', 23, 'Aucun', 3, NULL, 'Village fortifiée', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(88, '49', 'Safdar', 23, 'Atessa Safdar', 0, NULL, NULL, 'ACTIF', 0, 1713, '2023-12-29 12:56:41'),
(89, '44', 'Terre vierge', 23, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(90, '42', 'Brandebourg', 23, 'Théobald VI de Brandebourg', 0, NULL, 'Une baronnie célèbre pour ses prouesses commerciales. Sir Théobald est entre autre responsable pour avoir négocier les aspects commerciaux du fameux Traité d\'Altembourg et d\'être le détenteur aurélois de la route commerciale mise sur pied avec la Maison de Versan. ', 'ACTIF', 0, 4835, '2023-12-29 12:56:41'),
(91, '37', 'Cité d\'Andrave', 23, 'Aucun', 10, NULL, 'Cité universitaire fortifiée', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(92, '33', 'Terre vierge', 23, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(93, '38', 'Terre vierge', 23, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(94, '45', 'Dum’Badar', 23, 'Logan Badar', 8, NULL, 'Grande cité udar', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(95, '29', 'Carquechesne', 25, 'Aucun', 5, NULL, 'Ville forte', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(96, '34', 'Terre vierge', 25, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(97, '30', 'Terre vierge', 25, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(98, '31', 'Terre vierge', 25, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(99, '35', 'Terre vierge', 25, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(100, '39', 'Sardeyne', 25, 'Aucun', 2, NULL, 'Village', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(101, '46', 'Valaine', 25, 'Vitale Carderonne', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(102, '47', 'Baronnie des Vents', 25, 'Zedritch Van Aue', 3, NULL, 'Village', 'ACTIF', 0, 862, '2023-12-29 12:56:41'),
(103, '40', 'Terre vierge', 25, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(104, '48', 'Terre vierge', 25, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(105, '55', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(106, '50', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(107, '51', 'Bran Wor’ge Quarth', 24, 'Aucun', 8, NULL, 'Cité udar', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(108, '52', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(109, '53', 'Calande', 24, 'Aucun', 2, 'Alchimie', 'Calande est une ville dont la principale raison d\'être est la défense des Collines hurlantes. On y retrouve plusieurs guildes de chasseurs et de mercenaires dont un bon nombre se spécialisent dans la chasse aux bêtes féroces et autres imposantes « créatures ».<br/>\n<br/>\nL\'autre renommée de la région vient de sa production de catalyseurs alchimiques, prisés dans les grandes universités du pays et exportés à bon prix hors du royaume. Leur grande qualité vaut à cette baronnie relativement reculée la visite de plus d\'un académicien souhaitant garantir son approvisionnement en vu d\'expériences délicates.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(110, '54', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(111, '59', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(112, '58', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(113, '61', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(114, '60', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(115, '57', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(116, '56', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(117, '62', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(118, '65', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(119, '63', 'Forteresse de Cendrecourt', 24, 'Adélard Bonpoint', 5, NULL, 'Forteresse', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(120, '64', 'Terre vierge', 24, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(121, '66', 'Castelnau', 24, 'Florent de Castelnau', 2, NULL, 'Village fortifiée', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(122, '142', 'Noir-Fûtaille', 39, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(123, '143', 'Ville de Pontcaric', 39, 'Aucun', 6, NULL, 'Grande ville', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(124, '144', 'Markgrad', 10, 'Fob Rodstven', 5, NULL, 'Ville', 'ACTIF', 0, 3012, '2023-12-29 12:56:41'),
(125, '145', 'Sarvina', 39, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(126, '146', 'Banceint', 10, 'Eriol Tsujino', 2, NULL, 'Cité-prison pour le comté de Griveton, Banceint n\'abrite en fait qu\'un seul bâtiment : une gigantesque prison où l\'ancien peuple de Pontcaric et les prisonnier suffisamment malchanceux pour y être envoyés croupissent et travaillent dans la plus grande carrière du royaume. <br/>\n<br/>\nEntièrement couverte et protégée par d\'ingénieux mécaniques magiques, il n\'existe qu\'un seul moyen de sortir de Banceint : être racheté. Mourir même n\'est pas suffisant et les enfants des familles originellement enfermées en cet endroit poursuivent le travail également. Et bien que le Trône de l\'est ait acquis Banceint avec le changement d\'allégeance de Krabzs Ingni, des clauses ont été négociées afin de permettre à la « baronnie » de poursuivre ses activités sans trop de changement. La seule différence est donc que les prisonniers rachetés ne peuvent devenir esclaves.', 'ACTIF', 0, 7357, '2023-12-29 12:56:41'),
(127, '147', 'Jolènir', 10, 'Arthur l\'oublié', 1, NULL, 'Une baronnie lancée sur un nouveau départ dont la vocation semble vouloir être commerciale. Il s\'agit de la première terre à avoir pris l\'initiative d\'attirer l\'attention du Lavakhnir et de ses commerçants.', 'ACTIF', 0, 7235, '2023-12-29 12:56:41'),
(128, '148', 'Terre vierge', 16, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(129, '172', 'Terre vierge', 16, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(130, '149', 'Terre vierge', 16, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(131, '173', 'Néo-Sorvania', 16, 'Cyn de l\'Auge', 5, NULL, 'Ville de taille moyenne, il s\'agit du réputé quartier général des Tob Lebend, une organisation de nécromanciens de grande renommée qui n\'ont que peu d\'amour pour les non-Humains.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(132, '150', 'Université Akuma', 16, 'Sophia Markov', 8, NULL, 'Grande université spécialisée dans les Arts nécromantiques, l\'institution légendaire porte désormais également le nom de \"Université de l\'Augur\". ', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(133, '174', 'Cité de Caltagrad', 13, 'Aucun', 10, NULL, 'Capitale', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(134, '178', 'Nerbia', 13, 'Anabelle Vicari', 3, NULL, 'Ville portuaire', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(135, '183', 'Val d\'Ombre', 13, 'Viridis Hasseltiss', 1, NULL, NULL, 'ACTIF', 0, 196, '2023-12-29 12:56:41'),
(136, '182', 'Terre vierge', 13, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(137, '176', 'Terre vierge', 13, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(138, '175', 'Stensia', 16, 'Sophia Markov', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(139, '180', 'Caran', 12, 'Lucien Arènne', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(140, '187', 'Ville de Madelle', 12, 'Ascariss ', 6, 'Parchemins', 'Ville principale d\'Arlon, Madelle est bien entendu une ville où la magie nécromantique est au premier plan. La ville se distingue cependant par ses \"Parchemins écarlates\", qu\'elle fait produire par une petite armée d\'esclaves et qui, malgré leurs fabricants, sont fonctionnels tout en étant moins chers.', 'ACTIF', 1, 5559, '2023-12-29 12:56:41'),
(141, '198', 'Terre vierge', 12, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(142, '188', 'Terre vierge', 12, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(143, '194', 'Cité de Fortsand', 15, 'Henry Sombrecolline', 10, 'Artéfacts', 'Capitale commerciale', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(144, '204', 'Sydvest', 15, 'Aucun', 1, 'Expéditions', 'Baronnie gardant la Route du Sud pour Prospérance, Sydvest est une terre autrement plus aride que les autres fiefs prospérois. Elle est cependant la terre d\'accueil d\'une guilde particulièrement bien versée dans l\'organisation d\'expédition à l\'étranger.', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(145, '212', 'Rivadestir', 15, 'Sélène Casablanca', 4, 'Village collégiale', 'Village collégiale de grande envergure entièrement conçu et organisé par la maison Casablanca, Rivadestir cherche à se montrer plus accueillante envers les déistes et démonistes cherchant à cohabiter. La quête de connaissance est leur objectif principal, et la baronne considère que les querelles religieuses nuisent à celui-ci.', 'ACTIF', 0, 6484, '2023-12-29 12:56:41'),
(146, '211', 'Bronvess', 15, 'Arthur Andreev', 3, NULL, 'Village bucheron faisant la frontière avec la Forêt noire. L\'endroit prend tranquillement de l\'ampleur avec la construction d\'un grand colisée de bois qui servirait, dit-on, à tester les individus les plus courageux. Autrement, la situation géographique de Bronvess en fait un endroit que peu visite par erreur ou par divertissement.', 'ACTIF', 0, 5647, '2023-12-29 12:56:41'),
(147, '202', 'Caéssaria', 15, 'Neisseria', 3, 'Protection spirituelle', 'Baronnie faisant directement face à la Barrière du Seuil, la principale vocation de sa seigneuresse est de protéger le reste de Prospérance contre tout mouvement et toute menace en provenance de cette mystique frontière. En second lieu, l\'endroit sert de lieu d\'étude sur les phénomènes spirituels extrême.<br/>\n<br/>\nTous les cultes sont acceptés à Caéssaria, mais la nécromancie y est interdite vu la situation avec la Barrière.', 'ACTIF', 0, 3948, '2023-12-29 12:56:41'),
(148, '195', 'Grande Ours', 15, 'Méandre des Quatre-Vents', 0, NULL, NULL, 'ACTIF', 0, 6869, '2023-12-29 12:56:41'),
(149, '203', 'Sorénestine', 15, 'Altorian Larcohen', 3, 'Vin', 'Terre centrale de Prospérance, Sorénestine est également l\'une des plus fertile. Couverte de vigneraies au centre desquels trône une majestueuse villa, il s\'agit d\'un endroit paisible et dépurvu de grand centre urbain. Les seigneurs des lieux ne reçoivent que peu de visite en dehors des doléances de leur propre population et les gens qui n\'y sont pas invité ne sont pas les bienvenus s\'il n\'y ont pas affaire.', 'ACTIF', 0, 18, '2023-12-29 12:56:41'),
(150, '190', 'Terre vierge', 34, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(151, '191', 'Terre vierge', 34, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(152, '184', 'Ville de Lancedor', 34, 'Makkyël Der\'Oth', 5, NULL, 'Ville fortiée autrefois située au centre du Conclave avant l\'indépendance de Prospérance, Lancedor étais alors une plaque tournante pour le commerce de toutes les sortes : outils, armement, bijoux, esclaves, objets de tous les jours... Une grande partie de ce commerce a aujourd\'hui été détourné, mais Lancedor reste une grande ville. Étant la dernière cité néovienne sur la route du Sud, les denrée en provenant de Prospérance et du Lavakhnir y trouvent leurs premiers acheteurs. ', 'ACTIF', 1, 5994, '2023-12-29 12:56:41'),
(153, '192', 'Terre vierge', 15, 'Aucun', 0, 'Produits du Lavakhnir', 'Poste de commerce', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(154, '193', 'Les Milles-piques', 34, 'Skritt', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(155, '214', 'Terre vierge', 35, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(156, '209', 'Terre vierge', 35, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(157, '201', 'Terre vierge', 35, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(158, '210', 'Terre vierge', 35, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(159, '215', 'Terre vierge', 35, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(160, '206', 'Terre vierge', 14, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(161, '207', 'Terre vierge', 14, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(162, '213', 'Terre vierge', 14, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(163, '208', 'Terre vierge', 14, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(164, '199', 'Terre vierge', 14, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(165, '200', 'Terre vierge', 14, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(166, '189', 'Fort Gaumond', 14, 'Aucun', 2, NULL, 'Fort', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(167, '170', 'Ninkilim', 8, 'Paskinel Encre d\'Or', 2, NULL, 'Terre de refuge pour les Raskars exilés de Novembre lors de la destruction de Ratgard, les survivants y sont organisés afin qu\'ils puissent y rebâtir une nouvelle vie et faire survivre la culture ratgardienne.<br/>\n<br/>\nLa terre accueille désormais toutes les institutions autrefois situées à Ratgard et y a construit des bâtiments dédiés à la sauvegarde de ses trésors.', 'ACTIF', 0, 7494, '2023-12-29 12:56:41'),
(168, '179', 'Terre vierge', 11, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(169, '185', 'Ratgard', 11, 'Aucun', 1, NULL, 'Ruines béantes dans le paysage de Novembre, l\'ancienne cité-joyau des Raskars fut complètement détruite par une arme terrible connue sous le nom de l\'Opale pourpre. À ce jour, personne n\'habite le cratère laissée pour preuve de la puissance de cette arme.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(170, '186', 'Vieilesprit', 11, 'Hyun-Ae Tokugawa', 2, NULL, 'Village shataïen.', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(171, '197', 'Terre vierge', 11, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(172, '205', 'Terre vierge', 11, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(173, '196', 'Zul-Gorm', 11, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(174, '171', 'Palais de Velours', 8, 'Mukmu Dit le Lettré', 6, NULL, 'Palais khalien', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(175, '169', 'Ruiselle', 8, 'Aucun', 3, NULL, 'Village', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(176, '168', 'Terre vierge', 6, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(177, '165', 'Luademel', 8, 'Perendithas Du Tripot', 1, NULL, 'Hameau', 'ACTIF', 1, 5660, '2023-12-29 12:56:41'),
(178, '167', 'Terre vierge', 6, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(179, '163', 'Setvald', 6, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(180, '158', 'Terre vierge', 6, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(181, '154', 'Terre vierge', 7, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(182, '155', 'Origot', 6, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(183, '159', 'Terre vierge', 6, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(184, '164', 'Terre vierge', 6, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(185, '160', 'Des Aulnes', 6, 'Aucun', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(186, '166', 'La Fosse', 8, 'Aucun', 8, NULL, 'Cité', 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(187, '161', 'Nérium', 8, 'Henry Monarte', 0, NULL, 'Retraite personnelle du comte Henry Monarte, il s\'agit autrement d\'une terre peu peuplée.', 'ACTIF', 1, 334, '2023-12-29 12:56:41'),
(188, '162', 'Inniskovia', 8, 'Edwin Hebb', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41'),
(189, '157', 'St-Léon', 37, 'Sillas De Velours', 5, NULL, 'Ville comtale de Francourt.', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(190, '156', 'Porte-des-Brumes', 37, 'Alfred «l\'Avare» Balthazar', 2, NULL, 'Village', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(191, '152', 'Syptosis', 37, 'Alarick', 2, NULL, 'Forteresse.', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(192, '153', 'Hyden', 37, 'Henry De La Cour', 3, NULL, 'Grand village', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(193, '177', 'Heiwamura', 13, 'Tanaka Nakamura Tamahiro & Tanaka Nakamura Tetsuko', 6, NULL, 'Ville', 'ACTIF', 0, NULL, '2023-12-29 12:56:41'),
(194, '181', 'Riev', 12, 'Shepard Grey', 0, NULL, NULL, 'ACTIF', 1, NULL, '2023-12-29 12:56:41');

-- --------------------------------------------------------

--
-- Table structure for table `comtes`
--

CREATE TABLE `comtes` (
  `Id` int NOT NULL,
  `Nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Type` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `Couleur` varchar(7) COLLATE utf8mb4_general_ci DEFAULT '#FFFFFF',
  `CodeDuche` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `Dirigeant` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DescriptionDirigeant` longtext COLLATE utf8mb4_general_ci,
  `Scribe` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DescriptionScribe` longtext COLLATE utf8mb4_general_ci,
  `CodeEtat` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `IndQuete` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comtes`
--

INSERT INTO `comtes` (`Id`, `Nom`, `Type`, `Couleur`, `CodeDuche`, `Dirigeant`, `DescriptionDirigeant`, `Scribe`, `DescriptionScribe`, `CodeEtat`, `IndQuete`) VALUES
(0, 'Non défini', 'N.D.', '#FFFFFF', 'ND', 'N.D.', 'N.D.', 'N.D.', '', 'INACT', 1),
(1, 'Solèce', 'Comté', '#ff6f00', 'PLEINVENT', 'Bertrand de Polignac', 'Intendant de Gabrielle d\'Amerolles au moment de son assassinat, sir Bertrand a pris les rennes du comté à la place de feu sa reine. Il espère se démarquer dans ce rôle dans l\'espoir de conserver le titre.', 'Élios Grandura, servant jaune de Xobar Xan Zxenxen. Sous pression de plusieurs rebelles (demande de ', 'Plein-Vent', 'ACTIF', 1),
(2, 'Jade', 'Comté', '#ff6f00', 'PLEINVENT', 'Ko-Kei Nakamura', 'Priant de Chaos qui est loyal à sa famille. Il veut dominer les démonistes pour la plus grande gloire des Nakamura.', 'Élyna de Fretel, secrétaire personnelle du Comte de Jade.', 'Plein-Vent', 'ACTIF', 1),
(3, 'Braise-Ardente', 'Comté', '#ff6f00', 'PLEINVENT', 'Buck de Ké', 'Ancien membre des GaraKés, Buck de Ké est réputé pour son sadisme mais aussi pour son indifférence des affaires courantes de son comté. Ainsi, il tolère très bien les priants de dieux... puisqu’il s’en fiche.', 'Galien, scribe gobelin.', 'Plein-Vent', 'ACTIF', 1),
(4, 'Bonrempart', 'Comté', '#019306', 'TAURE', 'Allix Eikugoronoblashiverol', 'Ce vieux chapardeur est le premier de sa race, dans l’histoire de Bélénos, à obtenir un poste d’importance. Ami de Nostrum, il a combattu à ses côté dans la Guerre des Faux-Prophête et arbore depuis un air plus sérieux.', 'Takalan Coldabor', '', 'ACTIF', 1),
(5, 'Grands-Moulins', 'Comté', '#ff6f00', 'PLEINVENT', 'William', 'Affilié à une branche extrémiste du souffle du peuple, William est très près de son peuple et essaie tant bien que mal de sortir du lot de priants démonistes autour de lui. Priant d\'Ayka, il n\'hésite pas à faire régner une justice sévère dans le comté mais étant un peu oisif de nature, les problèmes peuvent s\'accumuler rapidement avant que celui-ci ne les traite.\r\n', 'Pascal Tirain', 'Plein-Vent', 'ACTIF', 1),
(6, 'Sanschênes', 'Comté', '#ff6f00', 'CARTHAME', 'Sigrid', 'Grande cultiste de Godtak, elle est une personne extrêmement contrôlante qui adore que les choses fonctionnent à sa façon. Elle n’hésite jamais à réprimer dans l’horreur et le sang les contestations ou même l’incompétence. Elle sait cependant reconnaître un travail bien fait. ', 'Aline Latendre, exécutrice de Sigrid.', 'Catharme', 'ACTIF', 1),
(7, 'Val-aux-Princes', 'Comté', '#ffc800', 'CHAMPAGNOL', 'Robert IV de Champagnol', 'Depuis l\'assassinat de Gaëlle Flores en 769, le comté a été mis sous la tutelle de Robert IV de Champagnol, régent actuel du Duché.', 'Olivier, scribe de Gaëlle Flore.', 'Champagnol', 'ACTIF', 1),
(8, 'Premier-Trône', 'Comté', '#ff6f00', 'CARTHAME', 'Mukmu Dit le Lettré', 'Mukmu était le comte de Griveton, mais il a quitté le règne de Néovia. Nul le sait comment il s’en est sorti, mais il a pu quitter Pontcaric avec tous ses hommes sans difficultés.\r\n\r\nConnaissant bien les compétences de ce comte, Xobar offrira la possibilité à Mukmu de devenir comte du premier-trône en échange de la reconstruction du palais de velours.', 'Sophie Laviolette, assistante de Maria', 'Catharme', 'ACTIF', 1),
(9, 'Boursicot', 'Comté', '#ff6f00', 'VERNAILLE', 'Blanche De Sénicourt', 'Générale des armées croisées de retour du Mhor\'kar, l\'Impérator est une vétéran aux talents incomparables lorsqu\'il est question de stratégie. Elle a su souder ensemble des hommes et des femmes de toutes races et religions et les ramener en Bélénos pour reconquérir Boursicot des mains auréloises.', 'Générale Rose Dostienne', 'Plein-Vent', 'ACTIF', 1),
(10, 'Cayonne', 'Comté', '#ff6f00', 'VERNAILLE', 'Fob Rodstven', 'N.D.', 'Platin, secrétaire de Muk’Mu', 'Rossignol', 'ACTIF', 1),
(11, 'Novembre', 'Comté', '#442244', 'ROSSIGNOL', 'Nikki Di Balsamo', 'Ratchar est un homme-rat avec des très nombreux enfants. Il a tout les stéréotypes des hommes-rats.', 'Indéterminé', '', 'ACTIF', 1),
(12, 'Arlon', 'Comté', '#442244', 'ROSSIGNOL', 'Shepard Grey', 'Shepard fut auparavant l’apprenti de Kaiji Zaratan, il est maintenant Comte d’Arlon. De nature sérieuse et pragmatique, Shepard voue un culte à Dagoth et s’attend à ce que ses subalternes risquent tout pour accomplir leurs tâches. Il est lui-même prêt à tout sacrifier pour obtenir ce qu’il veut. Shepard fait des recherches sur les nouvelles formes de magie et expérimente sur ses « enfants » de nouvelles transformations nécromantiques. Il espère ainsi pouvoir fournir à la famille Zaratan une armée encore plus efficace qu’elle l’a été autrefois.', 'Darek Sentry, Grand Inquisiteur de Dagoth. (Dirige surtout la partie militaire.)', 'Rossignol', 'ACTIF', 1),
(13, 'Longévia', 'Comté', '#442244', 'ROSSIGNOL', 'Viridis Hasseltiss', 'Vampire Amaï de seconde génération, Viridis est perçu comme l\'un des préférée de la reine. À son contraire, il est cependant de nature calme et son côté érudit transpire dès qu\'on lui demande d\'étaler ses connaissances, ce qu\'il fait avec plaisir tout en arborant systématiquement un petit air supérieur.', 'Inconnu.', 'Rossignol', 'ACTIF', 1),
(14, 'Morétoile', 'Comté', '#442244', 'ETOILES', 'Thianna Saphan ', 'Cette humaine, perçue par les Amaïs comme étant presque l\'une des leurs, s’est faite remarquer dans la guerre contre les Galléonites de Prospérance. Ses tractations avec La Peste Rasmussen Viatson mirent fin à la guerre et instaurèrent une relative paix. Elle est pleine de ressources et d’intelligence, les situations de crise l’amènent à se dépasser. Elle est en charge de la sécurité du duché via son réseau d’espions et d’informateurs et a un intérêt marqué pour le volet magique, si cher à ses véritables maîtres Amaï. Malgré son ambition, elle sait tenir sa place et accepte son rôle avec beaucoup d’enthousiasme… pour l’instant!\r ', 'Camille Serfisan, scribe officiel', 'Étoiles-du-sud', 'ACTIF', 1),
(15, 'Prospérance ', 'Comté', '#ffffff', 'PROSPER', 'Henry Sombrecolline', 'Nouveau dirigeant de Prospérance depuis le départ des Nordiens, l\'ascension d\'Henry au titre de régent se fit dans un coup d\'état soutenu par le Marché de Sable, le Conclave et les alliés de la famille Larcohen. Depuis, Prospérance devient peu à peu un havre commercial où tous les coups sont permis s\'ils servent les intérêts des Comtes de sable et des desseins qu\'ils ont pour leur nouvelle terre d\'accueil. Quant a Henry, il s\'agit d\'un monarche plutôt effacé. On le dit plus préoccupé par ses propres recherches arcaniques que par les grandes machinations de ses commanditaires.', 'Malia Stans', '', 'ACTIF', 1),
(16, 'Karskire', 'Comté', '#442244', 'ROSSIGNOL', 'Ensifero Hasseltiss', 'Successeur de Jinsuke Akuma depuis sa possession par un Thilian, Ensifero est lui aussi une liche. Amaï et fidèle à la déesse des secrets, son tempéramment est un peu plus passionné que le seigneur noctavien qui l\'a précédé. [...]', 'Sakuzen Zaratan', 'Rossignol', 'ACTIF', 1),
(17, 'Héodim', 'Comté', '#007bff', 'HEODIM', 'Dagon d\'Héodim', 'Dagon est l’ancien maire de Librebourg et est un très habile politicien. Il dirige le comté le plus peuplé de tout Bélénos et en est fier. Il est empli d’empathie envers les autres et n’hésite jamais à tendre la main à ceux qui le demandent.', 'Éliot, administrateur diplomatique de la capitale', 'Héodim', 'ACTIF', 1),
(18, 'Hautlangeois', 'Comté', '#007bff', 'HEODIM', 'Frédéric de Nithilmer de la Trémoille', 'Membre de la famille Trémoille, l’une des plus prestigieuses de l’Aurélius, Frédéric apprécie son rang et les honneurs qui viennent avec. Il apprécie les beaux atours et les belles choses, mais sa loyauté envers la couronne est totalement inébranlable. D’ailleurs, il est l’un des comtes les plus riches de Bélénos.', 'Élise de Nithilmer, au nom du Comte Frédéric de Nithilmer de la Trémoille', 'Héodim', 'ACTIF', 1),
(19, 'Sélarnes', 'Marquisat', '#007bff', 'HEODIM', 'Conall Frost McMonarld', 'Très humble et très proche du peuple, il est un guerrier saint d’Usire et est très axé vers la religion et vers son dieu. Il est également le confesseur de Sa Majesté. Conall pardonne souvent et sait bien écouter les autres. Sur un champ de bataille, il est redoutable. Il est l’un des pressentis pour mener les armées de Marussia dans le Royaume de Dagoth. ', 'Sœur Catherine, copiste de la Cathédrale Aédonite de Vertalia', 'Héodim', 'ACTIF', 1),
(20, 'Maillence', 'Marquisat', '#007bff', 'ROSELIERE', 'Isabelle de Méricourt', 'Très altruiste, Isabelle finance de nombreux orphelinats dans ses terres. Elle est une chevalière avec un très grand honneur qui ne recule devant rien pour respecter sa parole et sait mener des troupes avec brio. Elle prend toujours le temps de bien écrire et apprécie entretenir les bonnes correspondances.', 'Travis, Chevalier au service d’Isabelle de Méricourt', 'La Roselière', 'ACTIF', 1),
(21, 'Ardast', 'Comté', '#007bff', 'ROSELIERE', 'Aurel et Ophélie de Rosenglade', 'Couple comtale depuis l\'assension de Marussia comme reine de l\'Aurélius, le départ de cette dernière ne fit rien pour ébranler leur autorité sur le comté. à la fois généreux et avides de savoir, Aurel et Ophélie sont adorés des citoyens d\'Ardast, qui leur crédite la capacité du comté à faire face aux récentes difficultés.', 'Professeur Claude Toulain, au nom du couple comtale d’Ardast', 'La Roselière', 'ACTIF', 1),
(22, 'La Bastide', 'Comté', '#007bff', 'ROSELIERE', 'Bénédicte Delavigne', 'Bénédicte est une bélénoise patriotique très fière. Elle est plutôt égoïste dans ses demandes et ne se soucie que très peu de l’opinion des autres.', 'Dame Évelyne, dame de compagnie de Bénédicte Delavigne', 'La Roselière', 'ACTIF', 1),
(23, 'Markelus', 'Comté', '#007bff', 'ANLWICK', 'Agathella Brise-Étoiles', 'Successeur de Raphael Bardet, Tobermory est le nouvel Aurore de la cité souterraine d’Andrave et de sa prestigieuse université. Il s\'agit d\'un Nain qui ne manque pas de fierté ni de compétence, et comme avec son prédécesseur, tous les moyens sont bons pour rappeler aux bonnes gens qu\'Andrave n\'est seconde à aucune autre Université, particulièrement celle d\'Ardast. Il s\'agit d\'un dirigeant qui va droit au but et qui n\'accepte que rarement le compromis dans les résultats. ', 'Professeur Pirren, maître alchimiste de l’Université d’Andrave, pour l’Aurore Raphael Bardet.', 'Anlwick', 'ACTIF', 1),
(24, 'Cendrecourt', 'Marquisat', '#007bff', 'ANLWICK', 'Adélard Bonpoint', 'Grand prêtre galléonite, Adélard suivit le roi Amaury des terres du Nord jusqu\'à Champagnol, puis Prospérance et Cendrecourt pour venir reconquérir sa couronne. Il s\'agit d\'un homme inspirant pour ses confrères de culte, qui combine force, conviction et attitude positive dans la même personne.', 'Albert Duruisseaux, scribe pour Cliff Iziris, marquis de Cendrecourt', 'Anlwick', 'ACTIF', 1),
(25, 'Collines Hurlantes ', 'Comté', '#007bff', 'ANLWICK', 'Lazare Duchesne', 'Priant d\'Usire, marié à la prêtresse de Gaea Simone Duchesne. Il veut redorer le blason du comté entaché par les actions condamnées de Gaspard Fernèse, le précédant comte, destitué et enfermé en ce moment.', 'Alejandro l’Urdi, pour le comte des Collines Hurlantes.', 'Anlwick', 'ACTIF', 1),
(26, 'Plessisbourré', 'Comté', '#019306', 'TAURE', 'Alyona Klimova Routchkina', 'Ancienne stratège Aykanite, les mots et les chiffres n\'ont pas de secret pour être. Elle maîtrise parfaitement l\'art de la manipulation. Elle va préférer la méthode la plus efficace, même si elle prend plus de temps.', 'Roza Lobanova Perova', '', 'ACTIF', 1),
(27, 'Cendreterre', 'Comté', '#019306', 'TAURE', 'Alenia Yana Trradi', 'Grande prêtresse de Gaéa, Alénia du clan Yana\'drinn a été nommée à la tête de Cendrecourt après que la terre ait été reconquise aux peaux-vertes qui l\'infestaient. Parfois incroyablement sage et inspirée, parfois d\'humeur vengeresse, la nouvelle comtesse possède une inclinaison à demander le genre de faveur que personne n\'attend. ', 'Maldrihn, Elfe sauvage arcaniste', '', 'ACTIF', 1),
(29, 'Hautes-Terres', 'Comté', '#ffc800', 'CHAMPAGNOL', 'Albert Frontenay', 'Ce comté fait maintenant partie du nouveau royaume de Champagnol. Plus d\'informations à venir...', 'Inconnu', 'Champagnol', 'ACTIF', 1),
(30, 'Terres immortelles', 'Comté', '#019306', 'TAURE', 'Firost Ramanar', 'Ancien gardien de la forêt du secteur d’Hyden, Firost est l’un des cinq membres fondateurs du Conseil druidique et un des plus proches conseillers du Seigneur Nostrum. Il siège auprès du Conseil Druidique à Ainas Ilfirin.', 'Isilbor Mithrimar', '', 'ACTIF', 1),
(31, 'Harov', 'Comté', '#019306', 'TAURE', 'Lysandre Celepilinor\r ', 'Ambassadrice de la cour elfique et de la reine Filmalya, ne pouvant sortir de Bélénos suite à la fermeture des frontières des Grandes Nations. Lysandre hérita du comté d’Harov en attente de l’ouverture des frontières. Elle quittera le comté à la fin de l’année 768. Fervente priante de Sylva, ayant régné au côté de Sryou pendant plusieurs siècles, elle est une figure connue et importante pour les Taurë Ilfirinois.\r\n\r\nElle a apprécie fortement la structure et l’ordre dans son comté. Elle est très confiante de ses capacités et est très fière de son retour prochain en forêt noire.\r\n\r\nDESCRIPTION DU VI-COMTE :\r\n\r\nAykien pur et dur, soldat dans l\'âme, il a du apprendre la diplomatie sur le tas pour diriger le comté.\r\n', 'Dimitri Vladinovich Koureschov', '', 'ACTIF', 1),
(32, 'Eseldorf', 'Cité', '#cccccc', 'ESELDORF', 'Reinhart, Guide suprême', 'La cité a fermé ses portes de nouveau il y a deux ans, aucune information n\'est disponible sur celle-ci.', 'Inconnu', '', 'ACTIF', 0),
(34, 'Fort-Tremblant', 'Comté', '#442244', 'ETOILES', 'Rânaak', 'Orc auparavant esclave-éclaireur pour l’Hydre d’Amaï’ra, un groupe d’Amaï ayant terrorisé Hyden. Il eut un parcours sinueux mais fut un des rares à survivre à toutes les campagnes depuis la perte de Bran Worge Quarth contre l’Aurélius. Son expérience militaire fait de lui le commandant de toutes les forces du duché. Il jouit d’une excellente réputation auprès des Amaï, malgré son esprit indépendant. Sa loyauté ne peut être mise en doute. Son intérêt pour le chamanisme et surtout pour l’herboristerie le firent remarquer dès ses débuts. Pour un orc, il est posé, réfléchi et assez peu bagarreur, tendance accentuée par son âge avancé.  Son approche militaire personnelle est plutôt axée sur les actions de tirailleur mais il ne rechigne pas à ordonner une charge frontale au besoin.\r\n', 'Mathéo Lanvoie, scribe', 'Étoiles-du-sud', 'ACTIF', 1),
(35, 'Le Seuil', 'Territoire', '#442244', 'ETOILES', 'Loween et Nevan de l’Astre rêveur', 'Issus d’une secte vouée aux esprits les ayant endoctrinés dès leur jeune âge, Loween et Nevan n’ont que récemment ouvert leurs yeux à la présence des esprits.\r\n \r\nIls ont laissé derrière eux leurs précieuses familles pour mieux accomplir le rôle que les esprits leurs ont donné : aider la libération et l’épanouissement des esprits, que l’individu soit vivant ou non. Dans cette quête, ils ont également laissé derrière eux une part de leurs formes physiques pour devenir des Eidolons, des créatures mythiques à moitié présents dans chacun des deux mondes. Cette forme leur permet de régner sur le Seuil malgré la nature de sa population.', 'Inconnu', 'Étoiles-du-sud', 'ACTIF', 1),
(36, 'Flores', 'Comté', '#ffc800', 'CHAMPAGNOL', 'Anatole Sureau', 'Créé en l\'honneur de Gaëlle Flores suite à sa mort, ce comté marque les frontière nord-est de Bélénos et, par conséquent, est constamment soumis à des escarmouches provenant du Royaume du Nord. Il s\'agit autrement d\'une terre très fertile, comme le reste de Champagnol.', 'Inconnu', 'Champagnol', 'ACTIF', 1),
(37, 'Francourt', 'Comté', '#ff6f00', 'CARTHAME', 'Basil St-Germain, Puissant de Valaire Sartan', 'Suite à la mort de la reine Gabrielle d\'Amerolles, il revient à sir Jacob Larcohen de nommer le prochain comte ou la prochaine comtesse.', 'Le protégé ducal du moment.', '', 'ACTIF', 0),
(39, 'Griveton', 'Comté', '#ff6f00', 'VERNAILLE', 'Krabzs Ingni', 'Membre du Garm de Dagoth. Particulièrement vicieuse, sans pitié et très téméraire, elle prend des décisions impulsives. Tel que durant son règne à Bélénos, elle s’entoure de puissants atouts et utilise au maximum leur potentiel.\r\n \r\n Elle tient toujours parole, c’est de cette façon qu’elle arrive à construire sa force, son règne et à acquérir la confiance de ses alliés. Elle est très intègre, ce qui déstabilise parfois car au contraire de certains de ses pairs, elle n’a pas peur de dire ce qu’elle pense réellement.\r\n \r\n Proche du peuple, mais surtout pour mieux les comprendre, les asservir, trouver leur faiblesse et\r\n ceux à exploiter. \r\n \r\n Elle est avide de pouvoir et a un grand respect pour les êtres mythiques, raison\r\n pour laquelle elle accepte une gouverne sous le règne de Néovia\r\n \r\n Elle est  prête à tout pour obtenir encore plus de pouvoir.', 'N.D.', '', 'ACTIF', 1);

-- --------------------------------------------------------

--
-- Table structure for table `duches`
--

CREATE TABLE `duches` (
  `Code` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `Nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `CodeRoyaume` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `Dirigeant` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `NiveauProsperite` int NOT NULL DEFAULT '0',
  `Couleur` varchar(7) COLLATE utf8mb4_general_ci DEFAULT '#FFFFFF',
  `Description` longtext COLLATE utf8mb4_general_ci,
  `CodeEtat` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `duches`
--

INSERT INTO `duches` (`Code`, `Nom`, `CodeRoyaume`, `Dirigeant`, `NiveauProsperite`, `Couleur`, `Description`, `CodeEtat`) VALUES
('ANLWICK', 'Anlwick', 'AURELIUS', 'Tobermory Brise-Étoiles', 2, '#007bff', 'Duché plus exposé aux menaces extérieures de par son emplacement géographique, Anlwick est également parsemé de collines et de paysages plus accidentés, ce qui en fait à la fois une terre plus facile à défendre, mais aussi une terre moins propice pour l\'agriculture. Les gens qui y habitent sont généralement plus hardis que les autres auréloise, mais également plus calmes, moins prompt à la panique. La majorité de la communauté naine y habite, prêtant sa force et son expertise au duché, et on y trouve l\'ancestrale Andrave, cité de Markelus avant sa mort.', 'ACTIF'),
('CARTHAME', 'Carthame', 'TRONE', 'Mukmu Dit le Lettré', 3, '#ff6f00', '[Description Carthame]', 'ACTIF'),
('CHAMPAGNOL', 'Champagnol', 'CHAMPAGNOL', 'Robert IV de Champagnol', 2, '#ffc800', 'Champagnol, situé au nord-est de Bélénos, est un modeste duché agraire qui se distingue par sa forte effervescence populaire. On dit que c’est de la base de la société que provient une bonne partie des initiatives de réforme politique et de lutte contre les roayumes démonistes. Loin d\'être des conquérants, les terres qui constituent aujourd\'hui le Duché ont été libérées des successeurs de Dagoth en 768 sous l’impulsion d’armées principalement constitués de paysans et dirigées par la petite noblesse. Le Duché constitue aujourd\'hui un petit bastion de résistance où celui qui recherche une vie juste et simple peut y faire sa vie.', 'ACTIF'),
('ESELDORF', 'Eseldorf', 'ESELDORF', 'Reinhart, le Guide suprême', 1, '#ffffff', 'Dernière des trois anciennes Cités de l\'Ordre, Eseldorf est également appelée la Cité grise. Possédant la plus grande muraille jamais bâtie par des mains de mortels, la ville est réputée imprenable, tant par la terre que par la mer ou les souterrains. Ses grandes portes sont presque toujours fermées, ne s\'ouvrant que pour laisser partir des expéditions dont le retour n\'est généralement pas prévu, ou encore pour laisser passer les rares agents de liaison avec l\'extérieur.', 'ACTIF'),
('ETOILES', 'Étoiles-du-Sud', 'CONCLAVE', 'Lobran Rangard', 3, '#442244', 'Davatange sous l\'emprise des Amaï.', 'ACTIF'),
('HEODIM', 'Héodim', 'AURELIUS', 'Dagon d\'Héodim', 2, '#007bff', 'Verdoyantes et fertiles, les terres de ce duché protègent presque la moitié de la population auréloise en plus d\'être le siège du pouvoir. La plus grande route commerciale y est située, partant des frontières impériales jusqu\'au Marché Nicolet et apportant son lot de caravanes vers les autres duchés. Il s\'agit en échange du duché où la politique y est la plus féroce, bien différente des terres du nord et du sud. Certains disent qu\'il s\'agit là de la source de l\'unité auréloise, d\'autre qu\'il s\'agit de sa plus grande faiblesse.', 'ACTIF'),
('PLEINVENT', 'Plein-Vent', 'TRONE', 'Jacob Larcohen', 2, '#ff6f00', '[Description Plein-Vent]', 'ACTIF'),
('PROSPER', 'Prospérance', 'PROSPER', 'Henry Sombrecolline', 1, '#ffffff', 'Le comté occupé de Prospérance appartenait autrement au Conclave, mais fut récemment conquis par l\'armée nordienne d\'Amaury de Penthièvre, qui s\'en sert aujourd\'hui comme principal point de lancement pour sa campagne visant à reprendre le trône de l\'Aurélius. Dans cette occupation, le peuple vis misérablement et même les Amaï, qui dirigeaient autrefois dans l\'ombre, sont impuissants à reprendre ce territoire. Entretemps, Prospérance est devenu le nouveau siège de la Marque et de son Marché noir, y entreposant ses acquisitions et envoyant ses Marchands de sable partout en Bélénos pour les vendre.', 'ACTIF'),
('ROSELIERE', 'La Roselière', 'AURELIUS', 'Florence Dubrouillard', 3, '#007bff', 'Réputée pour ses lieux de savoirs, La Roselière est également connue comme étant le siège de plusieurs mouvements d\'indépendance, comme le Souffle du Peuple. Cela crée à la fois une division évidente entre les sages et les téméraires, ainsi qu\'une synergie intéressante entre ces deux caractères opposés. Il s\'agit donc d\'une terre moins tolérante envers les étrangers, particulièrement les Elfes et leurs loups-garous, mais également de la terre d\'accueil de l\'une des plus grandes université auréloise : Ardast. Qui plus est, la relative autonomie que les terres fertiles offrent aux habitants ne fait rien pour les convaincre d\'une plus grande ouverture et ne fait qu\'augmenter le sentiment de fierté qui règne chez les Roselois.', 'ACTIF'),
('ROSSIGNOL', 'Rossignol', 'CONCLAVE', 'Kaiji Zaratan', 3, '#442244', 'Davantage sous l\'influence des nécromanciens.', 'ACTIF'),
('TAURE', 'Taurë Ilfirin', 'TAURE', 'Firost Ramanar', 3, '#019306', 'Seules terres épargnées lors de la Guerre des Faux Prophètes, les Terres éternelles constitue le coeur et l\'âme de ce que l\'on nommait autrement « la Griffe de l\'Ouest ». Elle est principalement constituée de forêt dense, protégée par les Elfes, la magie et une horde de loups-garous sous le commandement du général Croc, et la rumeur veut que Dagoth lui-même n\'aurait pas réussit à y poser le pied.', 'ACTIF'),
('VERNAILLE', 'Vernaille', 'TRONE', 'Blanche de Sénicourt', 3, '#ff6f00', '[Description nouveau duché]', 'ACTIF');

-- --------------------------------------------------------

--
-- Table structure for table `royaumes`
--

CREATE TABLE `royaumes` (
  `Code` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `Nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Description` longtext COLLATE utf8mb4_general_ci,
  `CodeEtat` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `Couleur` varchar(7) COLLATE utf8mb4_general_ci DEFAULT '#FFFFFF',
  `IndQuete` int NOT NULL DEFAULT '1',
  `Dirigeant` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `royaumes`
--

INSERT INTO `royaumes` (`Code`, `Nom`, `Description`, `CodeEtat`, `Couleur`, `IndQuete`, `Dirigeant`) VALUES
('AURELIUS', 'Aurélius', 'À l’ouest de Bélénos s\'étend le Royaume d’Aurélius. Voué aux divinités de l’Ordre, l’Aurélius est un royaume d\'une grande stabilité marqué par les valeurs chevaleresques, ainsi qu\'une hiérarchie traditionnelle bien définie. Il s\'agit du plus grand royaume des terres et d\'un refuge pour tout ceux qui cherchent à fuir le chaos des autres royaumes.', 'ACTIF', '#007bff', 1, 'Amaury de Penthièvre'),
('AUTRES', 'Nations étrangères', 'L\'une des grandes nations étrangères ou territoires hotiles', 'ACTIF', '#FFFFFF', 0, NULL),
('CHAMPAGNOL', 'Champagnol', 'Champagnol, situé au nord-est de Bélénos, est un modeste duché agraire qui se distingue par sa forte effervescence populaire. On dit que c’est de la base de la société que provient une bonne partie des initiatives de réforme politique et de lutte contre les roayumes démonistes. Loin d\'être des conquérants, les terres qui constituent aujourd\'hui le Duché ont été libérées des successeurs de Dagoth en 768 sous l’impulsion d’armées principalement constitués de paysans et dirigées par la petite noblesse. Le Duché constitue aujourd\'hui un petit bastion de résistance où celui qui recherche une vie juste et simple peut y faire sa vie.', 'ACTIF', '#ffc800', 1, 'Robert IV de Champagnol'),
('CONCLAVE', 'Conclave', 'Issu d\'une fragile alliance entre nécromanciens et Amaïs, le Conclave occupe le sud-est du territoire bélénois. De taille comparable au Royaume de l\'Est, il est actuellement menée par une puissante vampire, Néovia Zarathan, et accueille une foulée de créatures de l\'ombre, ainsi que les mortels qui les supportent ou cherchent à faire usage de leurs pouvoirs. Magie, esclavage et une justice plutôt arbitraire étant au coeur du quotidien des citoyens, le Conclave n\'accueille évidemment que des démonistes dont les idéaux s\'alignent avec ceux de Noctave, Kaalkhorn ou Amaï\'ra.', 'ACTIF', '#442244', 1, 'Néovia Zaratan'),
('ESELDORF', 'Eseldorf', 'La dernière des trois Cités de l\'Ordre. Aussi appelée la Cité grise.', 'ACTIF', '#AAAAA', 0, 'Reinhart, le Guide suprême'),
('PROSPER', 'Prospérance', 'Prospérance. Le territoire d\'accueil du Marché noir bélénois.', 'ACTIF', '#7a7a7a', 1, 'Henry Sombrecolline'),
('TAURE', 'Taurë Ilfirin', 'Autrefois protecteur de l\'entièreté de la Griffe de l\'Ouest, le royaume sylvestre de Taurë Ilfirin est aujourd\'hui bien plus modeste depuis que Dagoth le conquérant fit brûler une large portion de la Forêt Noire. Ceci dit, ses forces n\'ont rien à envier à la puissance de ses voisins. Voué à la reconstruction de la Griffe et à une vie en harmonie avec la nature, la majorité de la population est elfique et gaéenne, avec un nombre non négligeable d\'aykanites humains et hommes-lézards.', 'ACTIF', '#019306', 1, 'Nostrum Gowan Mcmornald'),
('TRONE', 'Trône de l\'Est', 'Le Trône de l’Est, aussi nommé Royaume de Dagoth ou Royaume de l’Est, couvre une bonne partie de la partie est du territoire bélénois. Bien qu\'il soit majoritairement voué aux divinités de Chaos et contrôlé par leurs fidèles, c\'est le seul royaume qui accepte autant d\'avoir des citoyens déistes que démonistes.', 'ACTIF', '#ff6f00', 1, 'Blanche de Sénicourt');

-- --------------------------------------------------------

--
-- Table structure for table `trames`
--

CREATE TABLE `trames` (
  `Id` int NOT NULL,
  `IdComte` int NOT NULL,
  `Nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Description` longtext COLLATE utf8mb4_general_ci,
  `CodeEtat` varchar(5) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'REDAC',
  `IdCreateur` int NOT NULL,
  `DateCreation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trames`
--

INSERT INTO `trames` (`Id`, `IdComte`, `Nom`, `Description`, `CodeEtat`, `IdCreateur`, `DateCreation`) VALUES
(130, 15, 'Un peuple affamé', 'L’hiver a été rude, puisque suite à la libération des esclaves par l’Aurélius, le comté a cruellement manqué de main d’oeuvre agricole. Les réserves ont été insuffisantes et le peuple en a souffert. Peu de travailleurs ont été assez en santé pour effectuer les tâches agricoles ce qui mena a une mauvaise récolte a l\'automne 769. Nous semblons être dans un cercle vicieux qui nous entraîne très rapidement vers la mort des plus faibles et l\'affaiblissement des plus fort. La déchéance est notre lot.', 'ACTIF', 168, '2020-03-31 17:25:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `baronnies`
--
ALTER TABLE `baronnies`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Id_UNIQUE` (`Id`),
  ADD UNIQUE KEY `Cadastre_UNIQUE` (`Cadastre`);

--
-- Indexes for table `comtes`
--
ALTER TABLE `comtes`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `nomcomtes` (`Nom`);

--
-- Indexes for table `duches`
--
ALTER TABLE `duches`
  ADD PRIMARY KEY (`Code`),
  ADD UNIQUE KEY `Nom_UNIQUE` (`Nom`);

--
-- Indexes for table `royaumes`
--
ALTER TABLE `royaumes`
  ADD PRIMARY KEY (`Code`),
  ADD UNIQUE KEY `Nom_UNIQUE` (`Nom`);

--
-- Indexes for table `trames`
--
ALTER TABLE `trames`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `baronnies`
--
ALTER TABLE `baronnies`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
