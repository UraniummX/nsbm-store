SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` VALUES
(1,'Food & Snacks'),(2,'Stationery'),(3,'Apparel'),(4,'Tech & Gadgets'),(5,'Accessories');

DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `product_variants`;
DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `gallery_paths` text DEFAULT NULL,
  `status` enum('active','pre_order','out_of_stock') NOT NULL DEFAULT 'active',
  `stock_quantity` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `products` (`id`,`name`,`description`,`price`,`category_id`,`image_path`,`status`,`stock_quantity`) VALUES
(1,'NSBM Hoodie - Green','Official NSBM university hoodie in signature green. Premium fleece, unisex fit, kangaroo pocket.',3500.00,3,'assets/images/products/hoodie.jpg','active',10),
(2,'Classic White T-Shirt','100% cotton crew-neck tee with subtle NSBM chest logo. Comfortable everyday wear.',1500.00,3,'assets/images/products/tshirt.jpg','active',15),
(3,'Denim Jacket','Vintage-wash blue denim jacket. Relaxed fit with chest pockets. Perfect for campus style.',4500.00,3,'assets/images/products/jacket.jpg','active',8),
(4,'Canvas Tote Bag','Eco-friendly NSBM branded canvas tote bag. Strong handles, ideal for books and gear.',600.00,3,'assets/images/products/tote.jpg','active',20),
(5,'Cargo Pants','Multi-pocket tactical cargo pants. Durable ripstop fabric, functional for long campus days.',3800.00,3,'assets/images/products/pants.jpg','active',12),
(6,'Beanie Hat','Warm knitted beanie in NSBM green and black. Ribbed cuff, one size fits most.',1200.00,3,'assets/images/products/beanie.jpg','active',18),
(7,'Bucket Hat','90s style cotton bucket hat. Adjustable strap and UV protection brim.',1400.00,3,'assets/images/products/bucket_hat.jpg','active',14),
(8,'Jogger Sweatpants','Comfortable fleece-lined jogger pants with adjustable drawcord and ankle cuffs.',2800.00,3,'assets/images/products/jogger.jpg','active',10),
(9,'Belt Bag','Trendy waist pack with double-zip compartments. Great for hands-free campus life.',1800.00,3,'assets/images/products/belt_bag.jpg','active',9),
(10,'Windbreaker Jacket','Lightweight water-resistant jacket with a hood. Packable and stylish for rainy days.',3500.00,3,'assets/images/products/windbreaker.jpg','active',7),
(11,'Sports Socks Pack','Pack of 3 moisture-wicking athletic socks. Keeps feet fresh through long days.',950.00,3,'assets/images/products/socks.jpg','active',25),
(12,'Slim Leather Wallet','Genuine leather bi-fold wallet with RFID blocking. Slim profile, 6 card slots.',2200.00,3,'assets/images/products/wallet.jpg','active',11),
(13,'NSBM Lanyard','Woven NSBM branded lanyard with safety breakaway clip. Essential for every student.',350.00,3,'assets/images/products/lanyard.jpg','active',50),
(14,'Ribbed Tank Top','Breathable ribbed cotton tank top. Available in neutral tones, great for gym or casual wear.',1200.00,3,'assets/images/products/tank.jpg','active',16),
(15,'NSBM Polo Shirt','Limited edition NSBM embroidered polo shirt. Pre-order now to reserve your size.',2200.00,3,'assets/images/products/polo.jpg','pre_order',0),
(16,'NSBM Track Jacket','Official NSBM track jacket with full-zip, striped sleeves and embroidered crest. Pre-order.',4800.00,3,'assets/images/products/track.jpg','pre_order',0),

(17,'Customized Notebook','Spiral-bound A5 notebook with custom NSBM cover. 200 ruled pages, lay-flat binding.',450.00,2,'assets/images/products/notebook.jpg','active',30),
(18,'Sketchbook Pro','Hardcover A4 sketchbook for artists. 150 sheets of acid-free thick paper.',1200.00,2,'assets/images/products/sketchbook.jpg','active',15),
(19,'Mechanical Pencil Set','0.5mm lead mechanical pencils with comfort grip zone. Pack of 2.',250.00,2,'assets/images/products/pencil.jpg','active',40),
(20,'Acrylic Paint Set','Set of 24 vivid acrylic colors. Fast-drying, suitable for canvas and mixed media.',2800.00,2,'assets/images/products/acrylic.jpg','active',10),
(21,'Neon Highlighters Set','Set of 6 neon highlighters. Smear-proof ink, chisel tip works on all paper types.',550.00,2,'assets/images/products/highlighter.jpg','active',35),
(22,'Monthly Wall Planner','Large A2 dry-erase monthly calendar. Reusable grid with marker included.',1600.00,2,'assets/images/products/planner.jpg','active',12),
(23,'Fountain Pen','Elegant stainless steel fountain pen. Smooth italic nib. Comes with 2 ink cartridges.',1800.00,2,'assets/images/products/pen.jpg','active',8),
(24,'Sticky Notes Pack','5 colour-coded pads, 100 sheets each. Standard and large sizes included.',400.00,2,'assets/images/products/sticky.jpg','active',28),
(25,'Binder Clips Assorted','30-piece binder clip set. Three sizes: small, medium and large.',350.00,2,'assets/images/products/binder.jpg','active',22),
(26,'Geometry Set','Full precision geometry box. Compass, protractor, set square, and ruler.',1200.00,2,'assets/images/products/geometry.jpg','active',18),
(27,'Art Paint Brushes','Set of 12 nylon brushes. Flat, round, and detail tips for all paint media.',950.00,2,'assets/images/products/brushes.jpg','active',14),
(28,'Oil Pastels Box','Box of 48 vibrant oil pastels. Rich pigment, smooth blending on card and paper.',1500.00,2,'assets/images/products/oilpastel.jpg','active',10),
(29,'Origami Paper Pack','100 square sheets in 20 patterns. Perfect for art and craft projects.',600.00,2,'assets/images/products/origami.jpg','active',20),
(30,'Whiteboard Markers Set','Set of 4 dry-erase markers: red, blue, green and black. Fine tip.',750.00,2,'assets/images/products/whiteboard.jpg','active',24),
(31,'A4 Paper Bundle','500-sheet premium white A4 paper, 80gsm. Ideal for printing and photocopying. Pre-order.',1100.00,2,'assets/images/products/paper.jpg','pre_order',0),
(32,'Stapler Set','Mini desk stapler with 1000 staples. Compact design, locks for safe storage. Pre-order.',650.00,2,'assets/images/products/stapler.jpg','pre_order',0),

(33,'Wireless Ergonomic Mouse','2.4GHz wireless mouse. Silent click mechanism, contoured grip, 18-month battery.',1200.00,4,'assets/images/products/mouse.jpg','active',20),
(34,'Bluetooth Earbuds','Noise-cancelling TWS earbuds. IPX5 waterproof, 6hr playtime + 24hr case.',5500.00,4,'assets/images/products/earbuds.jpg','active',14),
(35,'Laptop Sticker Pack','Set of 20 premium waterproof vinyl stickers. Tech, anime and aesthetic themes.',150.00,4,'assets/images/products/sticker.jpg','active',50),
(36,'RGB Gaming Mouse','Wired RGB gaming mouse with 7 programmable buttons and 6400 DPI sensor.',2800.00,4,'assets/images/products/gaming_mouse.jpg','active',10),
(37,'Power Bank 10000mAh','Slim fast-charge power bank. Dual USB output, USB-C input, LED indicator.',3500.00,4,'assets/images/products/powerbank.jpg','active',15),
(38,'LED Study Lamp','Touch-control dimmable desk lamp. 3 brightness modes, USB charging port built in.',2400.00,4,'assets/images/products/lamp.jpg','active',9),
(39,'Bluetooth Keyboard','Slim rechargeable keyboard. Multi-device Bluetooth 5.0 pairing, quiet keys.',4200.00,4,'assets/images/products/keyboard.jpg','active',7),
(40,'External SSD 500GB','USB 3.1 portable SSD. 540MB/s read speed, shock-resistant aluminum shell.',12500.00,4,'assets/images/products/ssd.jpg','active',5),
(41,'Fitness Smart Watch','Fitness tracker with heart rate, sleep monitor and app notifications. 7-day battery.',7200.00,4,'assets/images/products/watch.jpg','active',6),
(42,'Adjustable Phone Stand','Foldable aluminum desk stand for phone and tablet. 360-degree rotation.',950.00,4,'assets/images/products/phone_stand.jpg','active',25),
(43,'Ergonomic Laptop Stand','Six-height aluminum laptop riser. Improves posture, foldable for travel.',3500.00,4,'assets/images/products/laptop_stand.jpg','active',8),
(44,'USB 3.0 Hub 4-Port','Compact 4-port USB hub with individual power switches. Plug and play.',1500.00,4,'assets/images/products/usb_hub.jpg','active',12),
(45,'GaN 65W Wall Charger','Compact GaN fast charger with USB-C and USB-A ports. Charges laptop and phone.',1800.00,4,'assets/images/products/charger.jpg','active',16),
(46,'XL Desk Mouse Pad','Extended gaming mat. Precision-tracking surface, anti-slip rubber base. 90x40cm.',500.00,4,'assets/images/products/deskpad.jpg','active',20),
(47,'Standalone VR Headset','All-in-one VR goggles for mobile gaming and 360-degree video. Pre-order now.',6500.00,4,'assets/images/products/vr.jpg','pre_order',0),
(48,'Mechanical Keyboard RGB','Full-size mechanical keyboard with tactile brown switches and per-key RGB. Pre-order.',8500.00,4,'assets/images/products/mech_keyboard.jpg','pre_order',0),

(49,'Bamboo Desk Plant','Low-maintenance bamboo plant in a ceramic pot. Livens up any desk space.',800.00,5,'assets/images/products/bamboo.jpg','active',18),
(50,'Non-Slip Yoga Mat','6mm thick exercise mat with alignment lines. Includes carry strap.',3800.00,5,'assets/images/products/yoga.jpg','active',10),
(51,'Lavender Scented Candle','Hand-poured soy wax candle. Natural lavender scent, 40-hour burn time.',900.00,5,'assets/images/products/candle.jpg','active',14),
(52,'Ceramic Coffee Mug','Hand-painted mug with motivational quote designs. 350ml capacity.',550.00,5,'assets/images/products/mug.jpg','active',22),
(53,'Insulated Travel Mug','Double-wall stainless tumbler. Keeps drinks hot or cold for 12 hours. 450ml.',2200.00,5,'assets/images/products/travel_mug.jpg','active',11),
(54,'Bento Lunch Box','4-compartment leakproof bento box. Eco-friendly, microwave-safe material.',1800.00,5,'assets/images/products/lunchbox.jpg','active',15),
(55,'Compact Folding Umbrella','Windproof auto-open umbrella. UV coating, collapsible to pocket size.',1500.00,5,'assets/images/products/umbrella.jpg','active',9),
(56,'Protein Shaker Bottle','600ml leak-proof blender bottle with stainless mixing ball. BPA-free.',1400.00,5,'assets/images/products/shaker.jpg','active',13),
(57,'Sherpa Throw Blanket','Ultra-soft 120x150cm blanket with sherpa fleece lining. Great for cold lecture halls.',2800.00,5,'assets/images/products/blanket.jpg','active',8),
(58,'Engraved Metal Keychain','Laser-engraved custom initial metal keychain. Durable and minimal.',450.00,5,'assets/images/products/keychain.jpg','active',35),
(59,'Bamboo Cutlery Set','Reusable utensil set: fork, knife, spoon, chopsticks and cleaning brush in a fabric case.',950.00,5,'assets/images/products/cutlery.jpg','active',20),
(60,'Handmade Bracelet','Handcrafted braided friendship bracelet. Adjustable, water-resistant cord.',500.00,5,'assets/images/products/bracelet.jpg','active',30),
(61,'Shockproof Phone Case','Military-grade drop-tested phone case. Raised camera and screen bezels.',1200.00,5,'assets/images/products/phone_case.jpg','active',25),
(62,'Neoprene Laptop Sleeve','Water-resistant sleeve for 13 and 15 inch laptops. Front pocket for accessories.',1800.00,5,'assets/images/products/sleeve.jpg','active',12),
(63,'Smart Insulated Bottle','Temperature display stainless bottle. Pre-order now — ships next month.',1800.00,5,'assets/images/products/smart_bottle.jpg','pre_order',0),
(64,'Minimalist Ring Set','Set of 5 stackable silver-tone rings. Pre-order for next collection drop.',1500.00,5,'assets/images/products/rings.jpg','pre_order',0),

(65,'Homemade Fudge Brownies','Freshly baked fudgy chocolate brownies. Set of 4, individually wrapped.',600.00,1,'assets/images/products/brownies.jpg','active',8),
(66,'Gourmet Cookie Box','Assorted artisan cookies baked fresh weekly. 12 per box, mixed flavours.',950.00,1,'assets/images/products/cookies.jpg','active',10),
(67,'Energy Bars Pack','Set of 5 natural oat and nut energy bars. No artificial flavours or sweeteners.',750.00,1,'assets/images/products/energy_bars.jpg','active',20),
(68,'Artisan Hot Sauce Set','Three bottles of homemade hot sauce: Mild, Medium and Fire. Perfect gift.',1200.00,1,'assets/images/products/hot_sauce.jpg','active',12),
(69,'Herbal Tea Blend','Premium loose-leaf herbal tea with calming chamomile and energising ginger mix.',850.00,1,'assets/images/products/tea.jpg','active',15),
(70,'Honey Oat Granola Bars','Pack of 6 honey-oat granola bars. High fibre, low sugar, great for study breaks.',650.00,1,'assets/images/products/granola.jpg','active',18),
(71,'Dark Chocolate Box','9 individually wrapped single-origin dark chocolate squares. 70% cocoa.',1100.00,1,'assets/images/products/chocolate.jpg','active',10),
(72,'Roasted Trail Mix','Mixed roasted nuts, dried cranberries and pumpkin seeds. 3 x 100g bags.',700.00,1,'assets/images/products/trail_mix.jpg','active',14),
(73,'Popcorn Variety Pack','3 flavours: Salted Caramel, Cheddar and Classic. Each 80g bag.',450.00,1,'assets/images/products/popcorn.jpg','active',25),
(74,'Premium Ramen Bundle','Pack of 6 premium instant ramen. Spicy and mild options included.',380.00,1,'assets/images/products/ramen.jpg','active',30),
(75,'Natural Peanut Butter','250g creamy natural peanut butter. No added sugar, salt or palm oil.',650.00,1,'assets/images/products/peanut.jpg','active',16),
(76,'Raw Honey Jar','350g pure raw honey sourced from local bee farms. Unfiltered and unheated.',950.00,1,'assets/images/products/honey.jpg','active',12),
(77,'Dried Fruit Mix','Mixed raisins, apricots and cranberries. 200g zip-lock pack.',750.00,1,'assets/images/products/dried_fruit.jpg','active',18),
(78,'Protein Oat Cookies','Box of 6 protein-packed oat cookies. 20g protein each. Chocolate chip flavour.',1200.00,1,'assets/images/products/protein_cookies.jpg','active',10),
(79,'Artisan Fruit Jam','Home-made strawberry and passion fruit jam. 200g glass jar. Pre-order.',550.00,1,'assets/images/products/jam.jpg','pre_order',0),
(80,'Japanese Matcha Kit','Ceremonial-grade matcha powder, bamboo whisk and ceramic bowl. Pre-order.',1500.00,1,'assets/images/products/matcha.jpg','pre_order',0);

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `variant_name` varchar(100) NOT NULL,
  `variant_type` varchar(50) NOT NULL,
  `price_modifier` decimal(10,2) DEFAULT 0.00,
  `stock_quantity` int(11) DEFAULT 10,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `product_variants` (`product_id`,`variant_name`,`variant_type`,`price_modifier`,`stock_quantity`) VALUES
(1,'Small','Size',0.00,8),(1,'Medium','Size',0.00,12),(1,'Large','Size',100.00,10),(1,'X-Large','Size',200.00,5),
(1,'Green','Color',0.00,15),(1,'Black','Color',0.00,10),(1,'Navy Blue','Color',0.00,10),
(2,'Small','Size',0.00,10),(2,'Medium','Size',0.00,15),(2,'Large','Size',0.00,12),(2,'X-Large','Size',100.00,5),
(2,'White','Color',0.00,20),(2,'Black','Color',0.00,12),
(3,'Small','Size',0.00,4),(3,'Medium','Size',0.00,6),(3,'Large','Size',200.00,5),(3,'X-Large','Size',300.00,2),
(5,'S (28-30)','Size',0.00,5),(5,'M (30-32)','Size',0.00,8),(5,'L (32-34)','Size',100.00,7),(5,'XL (34-36)','Size',200.00,4),
(8,'Small','Size',0.00,5),(8,'Medium','Size',0.00,8),(8,'Large','Size',0.00,6),(8,'X-Large','Size',100.00,3),
(10,'Small','Size',0.00,3),(10,'Medium','Size',0.00,5),(10,'Large','Size',100.00,4),(10,'X-Large','Size',200.00,2),
(10,'Black','Color',0.00,7),(10,'Olive Green','Color',0.00,5),(10,'Navy Blue','Color',0.00,6),
(7,'Black','Color',0.00,8),(7,'Beige','Color',0.00,6),(7,'Olive','Color',0.00,5),
(14,'Small','Size',0.00,6),(14,'Medium','Size',0.00,8),(14,'Large','Size',0.00,7),(14,'X-Large','Size',100.00,3),
(14,'White','Color',0.00,12),(14,'Black','Color',0.00,10),(14,'Grey','Color',0.00,8),
(33,'Matte Black','Color',0.00,12),(33,'Space Grey','Color',0.00,8),(33,'White','Color',200.00,5),
(34,'Matte Black','Color',0.00,8),(34,'White','Color',0.00,8),(34,'Navy Blue','Color',500.00,4),
(36,'Matte Black','Color',0.00,8),(36,'White','Color',0.00,4),
(37,'10000mAh','Capacity',0.00,10),(37,'20000mAh','Capacity',1500.00,5),
(41,'Black Band','Color',0.00,5),(41,'Blue Band','Color',0.00,4),(41,'Rose Gold Band','Color',500.00,2),
(43,'Space Grey','Color',0.00,5),(43,'Matte Black','Color',0.00,6),(43,'Silver','Color',200.00,3),
(40,'500GB','Capacity',0.00,4),(40,'1TB','Capacity',6000.00,2),
(50,'Purple','Color',0.00,5),(50,'Blue','Color',0.00,4),(50,'Black','Color',0.00,4),(50,'Green','Color',200.00,3),
(61,'iPhone 14/15','Model',0.00,10),(61,'Samsung S22/S23','Model',0.00,8),(61,'Pixel 7/8','Model',0.00,5),
(62,'13 inch','Size',0.00,8),(62,'15 inch','Size',200.00,6);

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `orders` (`id`,`customer_name`,`customer_email`,`total_amount`,`status`,`created_at`) VALUES
(1,'Amal Perera','amal.perera@students.nsbm.ac.lk',5000.00,'completed','2026-06-01 10:23:11'),
(2,'Nimasha Fernando','nimasha.f@students.nsbm.ac.lk',8700.00,'completed','2026-06-03 14:15:00'),
(3,'Kasun Bandara','kasun.b@students.nsbm.ac.lk',3500.00,'completed','2026-06-05 09:45:30'),
(4,'Sachini Wickramasinghe','sachini.w@students.nsbm.ac.lk',12000.00,'completed','2026-06-07 11:00:00'),
(5,'Dinusha Ratnayake','dinusha.r@students.nsbm.ac.lk',2750.00,'completed','2026-06-08 16:30:22'),
(6,'Tharaka Jayasuriya','tharaka.j@staff.nsbm.ac.lk',6500.00,'completed','2026-06-10 08:12:44'),
(7,'Malsha Wijesekara','malsha.w@students.nsbm.ac.lk',4300.00,'completed','2026-06-11 13:00:00'),
(8,'Ravindu Kumarasinghe','ravindu.k@students.nsbm.ac.lk',9800.00,'completed','2026-06-12 15:55:10'),
(9,'Upuli Gunawardena','upuli.g@students.nsbm.ac.lk',1850.00,'completed','2026-06-14 10:10:10'),
(10,'Lakshan Dissanayake','lakshan.d@students.nsbm.ac.lk',5500.00,'completed','2026-06-15 12:30:00'),
(11,'Harshani Madushanka','harshani.m@students.nsbm.ac.lk',3200.00,'pending','2026-06-18 09:05:19'),
(12,'Gihan Rathnayaka','gihan.r@staff.nsbm.ac.lk',7600.00,'pending','2026-06-19 14:22:08'),
(13,'Kavindi Amarasinghe','kavindi.a@students.nsbm.ac.lk',950.00,'pending','2026-06-20 11:40:35'),
(14,'Buddhika Silva','buddhika.s@students.nsbm.ac.lk',4800.00,'pending','2026-06-21 16:15:00'),
(15,'Oshada Premachandra','oshada.p@students.nsbm.ac.lk',2200.00,'completed','2026-06-22 08:55:20'),
(16,'Thilini Rupasinghe','thilini.r@students.nsbm.ac.lk',6300.00,'completed','2026-06-24 10:00:00'),
(17,'Madura Gunasekara','madura.g@students.nsbm.ac.lk',1200.00,'cancelled','2026-06-25 17:20:45'),
(18,'Sithara Weerasekara','sithara.w@students.nsbm.ac.lk',3900.00,'completed','2026-06-26 09:33:00'),
(19,'Yasiru Karunarathna','yasiru.k@students.nsbm.ac.lk',750.00,'pending','2026-06-28 13:45:12'),
(20,'Dilhani Samaraweera','dilhani.s@staff.nsbm.ac.lk',8500.00,'completed','2026-06-29 11:10:00');

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT 0,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `order_items` (`order_id`,`product_id`,`variant_id`,`quantity`,`price`) VALUES
(1,1,0,1,3500.00),(1,2,0,1,1500.00),
(2,34,0,1,5500.00),(2,17,0,2,900.00),(2,21,0,2,1100.00),(2,26,0,1,1200.00),
(3,1,0,1,3500.00),
(4,40,0,1,12500.00),
(5,24,0,2,800.00),(5,67,0,3,2250.00),
(6,47,0,1,6500.00),
(7,5,0,1,3800.00),(7,11,0,1,950.00),
(8,41,0,1,7200.00),(8,37,0,1,3500.00),
(9,66,0,1,950.00),(9,68,0,1,1200.00),
(10,34,0,1,5500.00),
(11,3,0,1,4500.00),(11,6,0,1,1200.00),
(12,39,0,1,4200.00),(12,42,0,1,950.00),(12,46,0,1,500.00),(12,58,0,2,900.00),
(13,66,0,1,950.00),
(14,16,0,1,4800.00),
(15,53,0,1,2200.00),
(16,33,0,1,1200.00),(16,37,0,1,3500.00),(16,46,0,1,500.00),(16,69,0,1,850.00),
(17,1,0,1,3500.00),(17,2,0,1,1200.00),
(18,50,0,1,3800.00),(18,49,0,1,800.00),
(19,67,0,1,750.00),
(20,48,0,1,8500.00);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` VALUES (1,'storeAdmin','$2y$10$HHSGaHs3LC1EZDntMIGUi.W/fsfmwqAmpbn8UBp8MuURxAtD3Ci6S','2026-06-18 09:02:46');

SET FOREIGN_KEY_CHECKS=1;
