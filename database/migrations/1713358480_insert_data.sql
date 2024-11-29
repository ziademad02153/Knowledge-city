-- up
INSERT INTO `categories` (`id`, `name`, `parent_id`) VALUES
('1c2a3b4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d', 'Technology', NULL),
('2c3d4e5f-6a7b-8c9d-0e1f-2a3b4c5d6e7f', 'Software Development', '1c2a3b4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d'),
('3d4e5f6a-7b8c-9d0e-1f2a-3b4c5d6e7f8a', 'Hardware Engineering 2', '2c3d4e5f-6a7b-8c9d-0e1f-2a3b4c5d6e7f'),
('3d4e5f6a-7b8c-9d0e-1f2a-3b4c5d6e7f82', 'Hardware Engineering 3', '3d4e5f6a-7b8c-9d0e-1f2a-3b4c5d6e7f8a');

-- down
DELETE FROM `categories`;
