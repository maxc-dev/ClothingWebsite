DROP DATABASE IF EXISTS maxc_dev_localhost;

CREATE DATABASE maxc_dev_localhost;
USE maxc_dev_localhost;

CREATE TABLE IF NOT EXISTS Brand (
	BrandID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    BrandName VARCHAR(32) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Category (
	CategoryID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(8) NOT NULL
);

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

CREATE TABLE IF NOT EXISTS Colour (
	ColourID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ColourName VARCHAR(16) NOT NULL
);

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

CREATE TABLE IF NOT EXISTS Size (
	SizeID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    SizeName VARCHAR(8) NOT NULL UNIQUE
);

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
    Length ENUM('Standard', 'Thigh Length', 'Knee Length', 'Calf Length') NOT NULL,
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

CREATE TABLE IF NOT EXISTS User (
	UserID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	Username VARCHAR(64) NOT NULL UNIQUE, 
	Email VARCHAR(128) NOT NULL UNIQUE,
	Password VARCHAR(32) NOT NULL,
	PhoneNumber VARCHAR(12) UNIQUE
);

CREATE TABLE IF NOT EXISTS Purchase (
	PurchaseID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ItemID INT NOT NULL,
    ColourID INT NOT NULL,
    Quantity SMALLINT NOT NULL DEFAULT 1,
    CONSTRAINT `fk_purchase_item`
		FOREIGN KEY (ItemID) REFERENCES Item (ItemID)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
    CONSTRAINT `fk_purchase_colour`
		FOREIGN KEY (ColourID) REFERENCES Colour (ColourID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS LinkPurchaseUser (
	PurchaseID INT NOT NULL,
    UserID INT NOT NULL,
    PurchaseCost DOUBLE NOT NULL,
    PRIMARY KEY(PurchaseID, UserID),
    CONSTRAINT `fk_link_purchase_purchase`
		FOREIGN KEY (PurchaseID) REFERENCES Purchase (PurchaseID)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
    CONSTRAINT `fk_link_user_purchase`
		FOREIGN KEY (UserID) REFERENCES User (UserID)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Review (
	PurchaseID INT NOT NULL PRIMARY KEY,
    Rating SMALLINT(1) NOT NULL DEFAULT 5,
    Comment VARCHAR(512),
    CONSTRAINT `fk_review_purchase`
		FOREIGN KEY (PurchaseID) REFERENCES Purchase (PurchaseID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

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