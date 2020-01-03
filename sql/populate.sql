/*
	Populating the SQL tables.
*/

USE maxc_dev_localhost;
/*
populating the brands table.
*/
INSERT INTO Brand (BrandName) VALUES ("Emporio Armani"), ("Calvin Klein"), ("Champion"), ("Fred Perry"), ("Hollister"), ("Nike"), ("Ralph Lauren"), ("Superdry"), ("Ted Baker"), ("Tommy Hilfiger"), ("The North Face");

INSERT INTO Size (SizeName) VALUES ("Small"), ("Medium"), ("Large"), ("8"), ("9"), ("10"), ('28"'), ('30"'), ('32');

INSERT INTO Category (CategoryName) VALUES
("Jackets"), ("Hoodie"), ("T-Shirt"), ("Polo Shirt"), ("Jeans"), ("Joggers"), ("Trainers"), ("Boots");

INSERT INTO User (Username, Email, Password) VALUES ("Max", "max@gmail.com", "admin"), ("Elle", "elle@gmail.com", "fox"), ("Louis", "louis@gmail.com", "chief"), ("Ryan", "ryan@gmail.com", "ginger"), ("Vivi", "vivi@gmail.com", "nick");

INSERT INTO Colour (ColourName) VALUES ("Blue"), ("Red"), ("White"), ("Black"), ("Navy"), ("Grey"), ("Green");

INSERT INTO Item (OriginalPrice, CategoryID, Gender, BrandID) VALUES
(129.99, 2, "Male", 1),
(159.99, 2, "Male", 1);
 
INSERT INTO LinkItemColour (ItemID, ColourID) VALUES (1, 5), (2, 4);

INSERT INTO LinkItemSize (ItemID, ColourID, SizeID, SizeQuantity) VALUES
 (1, 5, 1, 10), (1, 5, 2, 12), (1, 5, 3, 16),
 (2, 4, 1, 5), (2, 4, 2, 3), (2, 4, 3, 11); 
 
 INSERT INTO ItemTop (ItemID, SleeveLength) VALUES (1, "Long"), (2, "Long");
 
INSERT INTO Discount (ItemID, ColourID, DiscountPercent) VALUES (1, 5, 10);