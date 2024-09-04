CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `product` (`product_id`, `product_name`, `price`, `stock`) VALUES
(1, 'Sofa', '10000.00', 34),
(2, 'Dining Table', '6000.00', 100),
(3, 'Dining Chair(set of 4)', '5000.00', 30),
(4, 'Mattress', '8000.00', 111);

ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
