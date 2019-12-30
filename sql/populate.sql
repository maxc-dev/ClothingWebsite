/*
	Populating the SQL tables.
*/

USE maxc_dev_localhost;
/*
populating the brands table.
*/
INSERT INTO Brand (BrandName) VALUES ("Emporio Armani"), ("Calvin Klein"), ("Champion"), ("Fred Perry"), ("Hollister"), ("Nike"), ("Ralph Lauren"), ("Superdry"), ("Ted Baker"), ("Tommy Hilfiger"), ("The North Face");

INSERT INTO User (Username, Email, Password) VALUES ("Max", "max@gmail.com", "admin"), ("Elle", "elle@gmail.com", "fox"), ("Louis", "louis@gmail.com", "chief"), ("Ryan", "ryan@gmail.com", "ginger"), ("Vivi", "vivi@gmail.com", "nick");

INSERT INTO Colour (ColourName) VALUES ("Blue"), ("Red"), ("White"), ("Black"), ("Navy"), ("Grey"), ("Green");

INSERT INTO Item (ItemName, OriginalPrice, Category, Gender, BrandID) VALUES
("EA7 Large Logo Hoodie", 129.99, "Tops", "Male", 1),
("Large Eagle Logo Hoodie", 149.99, "Tops", "Male", 1);
 
INSERT INTO LinkItemColour (ItemID, ColourID) VALUES (1, 5), (2, 4);

INSERT INTO Size (ItemID, ColourID, SizeName, SizeQuantity) VALUES
-- (1, 5, "Small", 10), (1, 5, "Medium", 12), (1, 5, "Large", 16),
 (2, 4, "Small", 5), (2, 4, "Medium", 3), (2, 4, "Large", 11); 
 
INSERT INTO Discount (ItemID, ColourID, DiscountPercent) VALUES (1, 5, 20);