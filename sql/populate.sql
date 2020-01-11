USE maxc_dev_localhost;

-- The next few tables Brand, Size, Category & Color are static tables because they are given predefined records which don't change

-- adding all the brands into the brands table
INSERT INTO Brand (BrandName) VALUES ("Emporio Armani"), ("Calvin Klein"), ("Nike"), ("Ralph Lauren"), ("Superdry"), ("Ted Baker"), ("Timberland"), ("The North Face");

-- adding all the sizes to the size table
INSERT INTO Size (SizeName) VALUES ("Small"), ("Medium"), ("Large"), ("8"), ("9"), ("10"), ('28"'), ('30"'), ('32');

-- adding all the categories
INSERT INTO Category (CategoryName) VALUES
("Jackets"), ("Hoodie"), ("T-Shirt"), ("Polo Shirt"), ("Jeans"), ("Joggers"), ("Trainers"), ("Boots");

-- creates default colours
INSERT INTO Colour (ColourName) VALUES ("Blue"), ("Red"), ("White"), ("Black"), ("Navy"), ("Grey"), ("Green"), ("Yellow");

/*
	adding some dummy users.
    NOTE: The user 'Max' with UserID of 1 has a special admin status
    which gives access to user.php. Use the email and password for 'Max'
    to be able to see this page.
    
    Also, I am aware that storing passwords in pain text is a crime against humanity
    but for this small project I am going to keep it simple
*/
INSERT INTO User (Username, Email, Password) VALUES ("Max", "max@gmail.com", "admin"), ("Elle", "elle@gmail.com", "fox"), ("Louis", "louis@gmail.com", "chief"), ("Ryan", "ryan@gmail.com", "ginger"), ("Vivi", "vivi@gmail.com", "nick");

-- adding all the products
INSERT INTO Item (OriginalPrice, CategoryID, Gender, BrandID) VALUES
(129.99, 2, "Male", 1),
(159.99, 2, "Male", 1),
(89.99, 5, "Male", 2),
(89.99, 4, "Male", 4),
(39.99, 3, "Male", 2),
(349.99, 1, "Male", 4),
(94.99, 4, "Male", 4),
(34.99, 4, "Male", 7),
(89.99, 2, "Male", 2),
(614.99, 1, "Male", 4),
(99.99, 1, "Male", 5),
(79.99, 6, "Male", 8),
(114.99, 7, "Male", 6),
(164.99, 8, "Female", 7),
(129.99, 8, "Female", 7),
(44.99, 7, "Male", 3),
(54.99, 2, "Female", 5),
(34.99, 6, "Female", 3),
(234.99, 1, "Female", 4),
(134.99, 5, "Female", 4);
 
-- links all the items to different colours
INSERT INTO LinkItemColour (ItemID, ColourID) VALUES
 (1, 5), (2, 4), (3, 4), (4, 4), (4, 5), (5, 3), (5, 4), (5, 6), (6, 4), (7, 1), (7, 2), (7, 5), (7, 7), (8, 7), (9, 4), (10, 5),
 (11, 4), (12, 7), (13, 5), (14, 4), (15, 8), (16, 4), (17, 3), (18, 4), (19, 4), (20, 1);

-- specifies what products are available in what sizes, and how many
INSERT INTO LinkItemSize (ItemID, ColourID, SizeID, SizeQuantity) VALUES
 (1, 5, 1, 10), (1, 5, 2, 12), (1, 5, 3, 16),
 (2, 4, 1, 5), (2, 4, 2, 3), (2, 4, 3, 11),
 (3, 4, 7, 4), (3, 4, 8, 8),(3, 4, 9, 14),
 (4, 4, 2, 11), (4, 5, 3, 5), (4, 5, 2, 12),
 (5, 3, 2, 6), (5, 4, 2, 12), (5, 6, 1, 8),
 (6, 4, 3, 2), (7, 1, 2, 9), (8, 7, 1, 12),
 (8, 7, 2, 8), (9, 4, 1, 12), (9, 4, 2, 7),
 (10, 5, 2, 12), (10, 5, 1, 6), (11, 4, 1, 12),
 (11, 4, 2, 1), (12, 7, 9, 5), (12, 7, 8, 6),
 (13, 5, 4, 12), (14, 4, 5, 11), (15, 8, 6, 11),
 (16, 4, 5, 6), (16, 4, 4, 3), (17, 3, 2, 12),
 (17, 3, 1, 4), (18, 4, 1, 3), (18, 4, 2, 5),
 (19, 4, 3, 16), (19, 4, 2, 5), (20, 1, 7, 3);
 
-- defines additional information about a product into the seperate 'subclass' tables by linking the ID with an enum
INSERT INTO ItemTop (ItemID, SleeveLength) VALUES (1, "Long"), (2, "Long"), (4, "Short"), (5, "Short"), (6, "Long"),
 (7, "Long"), (8, "Short"), (9, "Long"), (10, "Long"), (11, "Long"), (17, "Long"), (19, "Long");

INSERT INTO ItemShirt (ItemID, Fit) VALUES (4, "Slim"), (5, "Regular"), (7, "Regular"), (8, "Regular");
 
INSERT INTO ItemJacket(ItemID, Length) VALUES (6, "Standard"), (10, "Thigh"), (11, "Standard"), (19, "Standard"); 
 
INSERT INTO ItemTrouser(ItemID, LegFit) VALUES (3, "Skinny"), (12, "Regular"), (18, "Regular");
 
INSERT INTO ItemShoe(ItemID, Fastener) VALUES (13, "Lace"), (14, "Lace"), (15, "Lace"), (16, "Lace"); 
 
-- adds discounts to certain products
INSERT INTO Discount (ItemID, ColourID, DiscountPercent) VALUES (1, 5, 10), (7, 4, 20), (10, 5, 40);