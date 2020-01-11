-- CREATING A BRAND NEW DATABASE

DROP DATABASE IF EXISTS maxc_dev_localhost;

CREATE DATABASE maxc_dev_localhost;
USE maxc_dev_localhost;

-- CREATING TABLES

-- table for clothing brands
CREATE TABLE IF NOT EXISTS Brand (
	BrandID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    BrandName VARCHAR(32) NOT NULL UNIQUE
);

-- table for catgories of clothes, such as Jackets or Shirts
CREATE TABLE IF NOT EXISTS Category (
	CategoryID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(16) NOT NULL
);

/*
	the most important table in the database. Item defines an
    individual product with price, release date, category, gender
    and brand. Other attributes such as size or colour are many to
    many and are subsequently defined in other link tables. Foreign keys
    have deletion protection since it neccesary.
*/
CREATE TABLE IF NOT EXISTS Item (
	ItemID INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
	OriginalPrice DOUBLE DEFAULT 0 NOT NULL,
    ReleaseDate DATE DEFAULT CURRENT_TIMESTAMP(0),
    CategoryID INT NOT NULL,
    Gender ENUM('Male', 'Female') DEFAULT 'Male' NOT NULL,
    BrandID INT DEFAULT 0 NOT NULL,
    CONSTRAINT `fk_item_brand`
		FOREIGN KEY (BrandID) REFERENCES Brand (BrandID)
		ON DELETE RESTRICT
		ON UPDATE CASCADE,
    CONSTRAINT `fk_item_category`
		FOREIGN KEY (CategoryID) REFERENCES Category (CategoryID)
		ON DELETE RESTRICT
		ON UPDATE CASCADE
);

-- table of all colours and their names with a composite primary key
CREATE TABLE IF NOT EXISTS Colour (
	ColourID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ColourName VARCHAR(16) NOT NULL
);

-- the link table that links an item with a colour
CREATE TABLE IF NOT EXISTS LinkItemColour (
	ItemID INT NOT NULL,
	ColourID INT NOT NULL,
    PRIMARY KEY (ItemID, ColourID),
    CONSTRAINT `fk_link_item_colour`
		FOREIGN KEY (ItemID) REFERENCES Item (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
    CONSTRAINT `fk_link_colour_item`
		FOREIGN KEY (ColourID) REFERENCES Colour (ColourID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- table of all possible clothes sizes
CREATE TABLE IF NOT EXISTS Size (
	SizeID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    SizeName VARCHAR(8) NOT NULL UNIQUE
);

/*
 links items and size with their colur to create a compositie primary key
 also has a SizeQuantity attribute with a default of 0 products.
 */
CREATE TABLE IF NOT EXISTS LinkItemSize (
	SizeID INT NOT NULL,
	ItemID INT NOT NULL ,
    ColourID INT NOT NULL,
    SizeQuantity TINYINT NOT NULL DEFAULT 0,
    PRIMARY KEY(SizeID, ItemID, ColourID),
    CONSTRAINT `fk_link_item_size`
		FOREIGN KEY (ItemID) REFERENCES Item (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE, 
    CONSTRAINT `fk_link_colour_size`
		FOREIGN KEY (ColourID) REFERENCES Colour (ColourID)
		ON DELETE CASCADE
		ON UPDATE CASCADE, 
    CONSTRAINT `fk_link_size_item`
		FOREIGN KEY (SizeID) REFERENCES Size (SizeID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

/*
 if an item has a discount, the item and colour are linked with a discount
 which defaults at 0. item and colour create a composite primary key
*/
CREATE TABLE IF NOT EXISTS Discount (
	ItemID INT NOT NULL,
	ColourID INT NOT NULL,
    DiscountPercent INT NOT NULL DEFAULT 0,
    PRIMARY KEY (ItemID, ColourID),
    CONSTRAINT `fk_link_item_discount`
		FOREIGN KEY (ItemID) REFERENCES Item (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
    CONSTRAINT `fk_link_colour_discount`
		FOREIGN KEY (ColourID) REFERENCES Colour (ColourID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

/*
these sql tables are subclasses in the eer diagram, it branches from Item, although
the sql table only needs to add one additional attribute to particular categories
of clothing. This applies for ItemTop, ItemJacket, ItemShirt, ItemShoe, ItemTrouser

*/
CREATE TABLE IF NOT EXISTS ItemTop (
	ItemID INT NOT NULL PRIMARY KEY,
    SleeveLength ENUM('No Sleeve', 'Short', 'Long') NOT NULL,
    CONSTRAINT `fk_top_item`
		FOREIGN KEY (ItemID) REFERENCES Item (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS ItemJacket (
	ItemID INT NOT NULL PRIMARY KEY,
    Length ENUM('Standard', 'Thigh', 'Knee', 'Calf') NOT NULL,
    CONSTRAINT `fk_jacket_top`
		FOREIGN KEY (ItemID) REFERENCES ItemTop (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS ItemShirt (
	ItemID INT NOT NULL PRIMARY KEY,
    Fit ENUM('Slim', 'Regular', 'Plus Size') NOT NULL,
    CONSTRAINT `fk_shirt_top`
		FOREIGN KEY (ItemID) REFERENCES ItemTop (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS ItemShoe (
	ItemID INT NOT NULL PRIMARY KEY,
    Fastener ENUM('Lace', 'Zip', 'Slip On', 'Strap') NOT NULL,
    CONSTRAINT `fk_shoe_item`
		FOREIGN KEY (ItemID) REFERENCES Item (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS ItemTrouser (
	ItemID INT NOT NULL PRIMARY KEY,
    LegFit ENUM('Skinny', 'Slim', 'Regular') NOT NULL,
    CONSTRAINT `fk_trouser_item`
		FOREIGN KEY (ItemID) REFERENCES Item (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- creates a table containing a user's information both userid, username, email and number are unique
CREATE TABLE IF NOT EXISTS User (
	UserID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	Username VARCHAR(64) NOT NULL UNIQUE, 
	Email VARCHAR(128) NOT NULL UNIQUE,
	Password VARCHAR(32) NOT NULL,
	PhoneNumber VARCHAR(12) UNIQUE
);

/*
this table contains all the purchase information. so for each item,colour,size,quantity,price a
new record saves it when it is bought.
*/
CREATE TABLE IF NOT EXISTS Purchase (
	PurchaseID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ItemID INT NOT NULL,
    ColourID INT NOT NULL,
    SizeID INT NOT NULL,
    Quantity SMALLINT NOT NULL DEFAULT 1,
    Price INT NOT NULL DEFAULT 0,
    CONSTRAINT `fk_purchase_item`
		FOREIGN KEY (ItemID) REFERENCES Item (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
    CONSTRAINT `fk_purchase_colour`
		FOREIGN KEY (ColourID) REFERENCES Colour (ColourID)
		ON DELETE CASCADE
		ON UPDATE CASCADE, 
    CONSTRAINT `fk_purchase_size`
		FOREIGN KEY (SizeID) REFERENCES Size (SizeID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- links the purchases between the user since the user can make multiple purchases
CREATE TABLE IF NOT EXISTS LinkPurchaseUser (
	PurchaseID INT NOT NULL,
    UserID INT NOT NULL,
    CONSTRAINT `fk_link_purchase_purchase`
		FOREIGN KEY (PurchaseID) REFERENCES Purchase (PurchaseID)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
    CONSTRAINT `fk_link_user_purchase`
		FOREIGN KEY (UserID) REFERENCES User (UserID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- if a user leaves a review, they need a verified purchase id, a rating and an optional comment
CREATE TABLE IF NOT EXISTS Review (
	PurchaseID INT NOT NULL PRIMARY KEY,
    Rating SMALLINT(1) NOT NULL DEFAULT 5,
    Comment VARCHAR(512),
    CONSTRAINT `fk_review_purchase`
		FOREIGN KEY (PurchaseID) REFERENCES Purchase (PurchaseID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- users can have multiple addresses so needs a new table with a foreign key to the user table
CREATE TABLE IF NOT EXISTS DeliveryAddress (
	UserID INT NOT NULL,
    HouseNumber INT NOT NULL,
    Street TEXT NOT NULL,
    City TEXT NOT NULL,
    PostCode TEXT NOT NULL,
    CONSTRAINT `fk_deliveryaddress_user`
		FOREIGN KEY (UserID) REFERENCES User (UserID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/*
this table stores all the information about each item added to a user's basket.
all attributes must be not null because they are later transferred to the purchase table
*/
CREATE TABLE IF NOT EXISTS Basket (
	UserID INT NOT NULL,
    ItemID INT NOT NULL,
    ColourID INT NOT NULL,
    SizeID INT NOT NULL,
    Quantity INT NOT NULL DEFAULT 1,
    PRIMARY KEY(UserID, ItemID, ColourID, SizeID),
    CONSTRAINT `fk_basket_user`
		FOREIGN KEY (UserID) REFERENCES User (UserID)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
    CONSTRAINT `fk_basket_item`
		FOREIGN KEY (ItemID) REFERENCES Item (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
    CONSTRAINT `fk_basket_colour`
		FOREIGN KEY (ColourID) REFERENCES Colour (ColourID)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
    CONSTRAINT `fk_basket_size`
		FOREIGN KEY (SizeID) REFERENCES Size (SizeID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- POPULATING THE DATABASE

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