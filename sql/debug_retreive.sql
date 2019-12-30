USE maxc_dev_localhost;

SELECT Item.ItemID, Item.ItemName, Item.OriginalPrice, Item.Category, Item.Gender, Brand.BrandName, Colour.ColourID, Colour.ColourName
 FROM Item
 INNER JOIN LinkItemColour ON Item.ItemID=LinkItemColour.ItemID
 INNER JOIN Colour ON Colour.ColourID=LinkItemColour.ColourID
 INNER JOIN Brand ON Brand.BrandID=Item.BrandID;