CREATE TABLE `barcode_prod` (
  `id` int NOT NULL,
  `prod_no` varchar(255) NOT NULL,
  `prod_desc` varchar(255) NOT NULL,
  `qty` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `duedate` date DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;