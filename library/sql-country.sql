INSERT INTO @DB_MYSQL_PREFIX@country
(country_id, country, alpha2, alpha3, `numeric`, title, tag, phone_code, status)
VALUES
(1, 'US', 'US', 'USA', 840, 'United States', 10, '1', NULL),
(2, 'CA', 'CA', 'CAN', 124, 'Canada', 5, '1', NULL),
(3, 'AF', 'AF', 'AFG', 4, 'Afghanistan', 0, '93', NULL),
(4, 'AL', 'AL', 'ALB', 8, 'Albania', 0, '355', NULL),
(5, 'DZ', 'DZ', 'DZA', 12, 'Algeria', 0, '213', NULL),
(6, 'AS', 'AS', 'ASM', 16, 'American Samoa', 0, '1-684', NULL),
(7, 'AD', 'AD', 'AND', 20, 'Andorra', 0, '376', NULL),
(8, 'AO', 'AO', 'AGO', 24, 'Angola', 0, '244', NULL),
(9, 'AI', 'AI', 'AIA', 660, 'Anguilla', 0, '1-264', NULL),
(10, 'AQ', 'AQ', 'ATA', 10, 'Antarctica', 0, '672', NULL),
(11, 'AG', 'AG', 'ATG', 28, 'Antigua and Barbuda', 0, '1-268', NULL),
(12, 'AR', 'AR', 'ARG', 32, 'Argentina', 0, '54', NULL),
(13, 'AM', 'AM', 'ARM', 51, 'Armenia', 0, '374', NULL),
(14, 'AW', 'AW', 'ABW', 533, 'Aruba', 0, '297', NULL),
(15, 'AU', 'AU', 'AUS', 36, 'Australia', 0, '61', NULL),
(16, 'AT', 'AT', 'AUT', 40, 'Austria', 0, '43', NULL),
(17, 'AZ', 'AZ', 'AZE', 31, 'Azerbaijan', 0, '994', NULL),
(18, 'BS', 'BS', 'BHS', 44, 'Bahamas', 0, '1-242', NULL),
(19, 'BH', 'BH', 'BHR', 48, 'Bahrain', 0, '973', NULL),
(20, 'BD', 'BD', 'BGD', 50, 'Bangladesh', 0, '880', NULL),
(21, 'BB', 'BB', 'BRB', 52, 'Barbados', 0, '1-246', NULL),
(22, 'BY', 'BY', 'BLR', 112, 'Belarus', 0, '375', NULL),
(23, 'BE', 'BE', 'BEL', 56, 'Belgium', 0, '32', NULL),
(24, 'BZ', 'BZ', 'BLZ', 84, 'Belize', 0, '501', NULL),
(25, 'BJ', 'BJ', 'BEN', 204, 'Benin', 0, '229', NULL),
(26, 'BM', 'BM', 'BMU', 60, 'Bermuda', 0, '1-441', NULL),
(27, 'BT', 'BT', 'BTN', 64, 'Bhutan', 0, '975', NULL),
(28, 'BO', 'BO', 'BOL', 68, 'Bolivia', 0, '591', NULL),
(29, 'BA', 'BA', 'BIH', 70, 'Bosnia and Herzegovina', 0, '387', NULL),
(30, 'BW', 'BW', 'BWA', 72, 'Botswana', 0, '267', NULL),
(31, 'BV', 'BV', 'BVT', 74, 'Bouvet Island', 0, NULL, NULL),
(32, 'BR', 'BR', 'BRA', 76, 'Brazil', 0, '55', NULL),
(33, 'IO', 'IO', 'IOT', 86, 'British Indian Ocean Territory', 0, '246', NULL),
(34, 'BN', 'BN', 'BRN', 96, 'Brunei', 0, '673', NULL),
(35, 'BG', 'BG', 'BGR', 100, 'Bulgaria', 0, '359', NULL),
(36, 'BF', 'BF', 'BFA', 854, 'Burkina Faso', 0, '226', NULL),
(37, 'BI', 'BI', 'BDI', 108, 'Burundi', 0, '257', NULL),
(38, 'KH', 'KH', 'KHM', 116, 'Cambodia', 0, '855', NULL),
(39, 'CM', 'CM', 'CMR', 120, 'Cameroon', 0, '237', NULL),
(40, 'CV', 'CV', 'CPV', 132, 'Cape Verde', 0, '238', NULL),
(41, 'KY', 'KY', 'CYM', 136, 'Cayman Islands', 0, '1-345', NULL),
(42, 'CF', 'CF', 'CAF', 140, 'Central African Republic', 0, '236', NULL),
(43, 'TD', 'TD', 'TCD', 148, 'Chad', 0, '235', NULL),
(44, 'CL', 'CL', 'CHL', 152, 'Chile', 0, '56', NULL),
(45, 'CN', 'CN', 'CHN', 156, 'China', 0, '86', NULL),
(46, 'CX', 'CX', 'CXR', 162, 'Christmas Island', 0, '61', NULL),
(47, 'CC', 'CC', 'CCK', 166, 'Cocos (Keeling) Islands', 0, '61', NULL),
(48, 'CO', 'CO', 'COL', 170, 'Colombia', 0, '57', NULL),
(49, 'KM', 'KM', 'COM', 174, 'Comoros', 0, '269', NULL),
(50, 'CG', 'CG', 'COG', 178, 'Congo', 0, '242', NULL),
(51, 'CK', 'CK', 'COK', 184, 'Cook Islands', 0, '682', NULL),
(52, 'CR', 'CR', 'CRI', 188, 'Costa Rica', 0, '506', NULL),
(53, 'CI', 'CI', 'CIV', 384, 'Cote d''Ivoire', 0, '225', NULL),
(54, 'HR', 'HR', 'HRV', 191, 'Croatia (Hrvatska)', 0, '385', NULL),
(55, 'CU', 'CU', 'CUB', 192, 'Cuba', 0, '53', NULL),
(56, 'CY', 'CY', 'CYP', 196, 'Cyprus', 0, '357', NULL),
(57, 'CZ', 'CZ', 'CZE', 203, 'Czech Republic', 0, '420', NULL),
(58, 'CD', 'CD', 'COD', 180, 'Congo (DRC)', 0, '243', NULL),
(59, 'DK', 'DK', 'DNK', 208, 'Denmark', 0, '45', NULL),
(60, 'DJ', 'DJ', 'DJI', 262, 'Djibouti', 0, '253', NULL),
(61, 'DM', 'DM', 'DMA', 212, 'Dominica', 0, '1-767', NULL),
(62, 'DO', 'DO', 'DOM', 214, 'Dominican Republic', 0, '1-809,1-829,1-849', NULL),
(63, 'TL', 'TL', 'TLS', 626, 'East Timor', 0, '670', NULL),
(64, 'EC', 'EC', 'ECU', 218, 'Ecuador', 0, '593', NULL),
(65, 'EG', 'EG', 'EGY', 818, 'Egypt', 0, '20', NULL),
(66, 'SV', 'SV', 'SLV', 222, 'El Salvador', 0, '503', NULL),
(67, 'GQ', 'GQ', 'GNQ', 226, 'Equatorial Guinea', 0, '240', NULL),
(68, 'ER', 'ER', 'ERI', 232, 'Eritrea', 0, '291', NULL),
(69, 'EE', 'EE', 'EST', 233, 'Estonia', 0, '372', NULL),
(70, 'ET', 'ET', 'ETH', 231, 'Ethiopia', 0, '251', NULL),
(71, 'FK', 'FK', 'FLK', 238, 'Falkland Islands (Islas Malvinas)', 0, '500', NULL),
(72, 'FO', 'FO', 'FRO', 234, 'Faroe Islands', 0, '298', NULL),
(73, 'FJ', 'FJ', 'FJI', 242, 'Fiji Islands', 0, '679', NULL),
(74, 'FI', 'FI', 'FIN', 246, 'Finland', 0, '358', NULL),
(75, 'FR', 'FR', 'FRA', 250, 'France', 0, '33', NULL),
(76, 'GF', 'GF', 'GUF', 254, 'French Guiana', 0, '594', NULL),
(77, 'PF', 'PF', 'PYF', 258, 'French Polynesia', 0, '689', NULL),
(78, 'TF', 'TF', 'ATF', 260, 'French Southern and Antarctic Lands', 0, '262,508,590,594,596', NULL),
(79, 'GA', 'GA', 'GAB', 266, 'Gabon', 0, '241', NULL),
(80, 'GM', 'GM', 'GMB', 270, 'Gambia', 0, '220', NULL),
(81, 'GE', 'GE', 'GEO', 268, 'Georgia', 0, '995', NULL),
(82, 'DE', 'DE', 'DEU', 276, 'Germany', 0, '49', NULL),
(83, 'GH', 'GH', 'GHA', 288, 'Ghana', 0, '233', NULL),
(84, 'GI', 'GI', 'GIB', 292, 'Gibraltar', 0, '350', NULL),
(85, 'GR', 'GR', 'GRC', 300, 'Greece', 0, '30', NULL),
(86, 'GL', 'GL', 'GRL', 304, 'Greenland', 0, '299', NULL),
(87, 'GD', 'GD', 'GRD', 308, 'Grenada', 0, '1-473', NULL),
(88, 'GP', 'GP', 'GLP', 312, 'Guadeloupe', 0, '590', NULL),
(89, 'GU', 'GU', 'GUM', 316, 'Guam', 0, '1-671', NULL),
(90, 'GT', 'GT', 'GTM', 320, 'Guatemala', 0, '502', NULL),
(91, 'GN', 'GN', 'GIN', 324, 'Guinea', 0, '224', NULL),
(92, 'GW', 'GW', 'GNB', 624, 'Guinea-Bissau', 0, '245', NULL),
(93, 'GY', 'GY', 'GUY', 328, 'Guyana', 0, '592', NULL),
(94, 'HT', 'HT', 'HTI', 332, 'Haiti', 0, '509', NULL),
(95, 'HM', 'HM', 'HMD', 334, 'Heard Island and McDonald Islands', 0, NULL, NULL),
(96, 'HN', 'HN', 'HND', 340, 'Honduras', 0, '504', NULL),
(97, 'HK', 'HK', 'HKG', 344, 'Hong Kong SAR', 0, '852', NULL),
(98, 'HU', 'HU', 'HUN', 348, 'Hungary', 0, '36', NULL),
(99, 'IS', 'IS', 'ISL', 352, 'Iceland', 0, '354', NULL),
(100, 'IN', 'IN', 'IND', 356, 'India', 0, '91', NULL),
(101, 'ID', 'ID', 'IDN', 360, 'Indonesia', 0, '62', NULL),
(102, 'IR', 'IR', 'IRN', 364, 'Iran', 0, '98', NULL),
(103, 'IQ', 'IQ', 'IRQ', 368, 'Iraq', 0, '964', NULL),
(104, 'IE', 'IE', 'IRL', 372, 'Ireland', 0, '353', NULL),
(105, 'IL', 'IL', 'ISR', 376, 'Israel', 0, '972', NULL),
(106, 'IT', 'IT', 'ITA', 380, 'Italy', 0, '39', NULL),
(107, 'JM', 'JM', 'JAM', 388, 'Jamaica', 0, '1-876', NULL),
(108, 'JP', 'JP', 'JPN', 392, 'Japan', 0, '81', NULL),
(109, 'JO', 'JO', 'JOR', 400, 'Jordan', 0, '962', NULL),
(110, 'KZ', 'KZ', 'KAZ', 398, 'Kazakhstan', 0, '7', NULL),
(111, 'KE', 'KE', 'KEN', 404, 'Kenya', 0, '254', NULL),
(112, 'KI', 'KI', 'KIR', 296, 'Kiribati', 0, '686', NULL),
(113, 'KR', 'KR', 'KOR', 410, 'Korea', 0, '82', NULL),
(114, 'KW', 'KW', 'KWT', 414, 'Kuwait', 0, '965', NULL),
(115, 'KG', 'KG', 'KGZ', 417, 'Kyrgyzstan', 0, '996', NULL),
(116, 'LA', 'LA', 'LAO', 418, 'Laos', 0, '856', NULL),
(117, 'LV', 'LV', 'LVA', 428, 'Latvia', 0, '371', NULL),
(118, 'LB', 'LB', 'LBN', 422, 'Lebanon', 0, '961', NULL),
(119, 'LS', 'LS', 'LSO', 426, 'Lesotho', 0, '266', NULL),
(120, 'LR', 'LR', 'LBR', 430, 'Liberia', 0, '231', NULL),
(121, 'LY', 'LY', 'LBY', 434, 'Libya', 0, '218', NULL),
(122, 'LI', 'LI', 'LIE', 438, 'Liechtenstein', 0, '423', NULL),
(123, 'LT', 'LT', 'LTU', 440, 'Lithuania', 0, '370', NULL),
(124, 'LU', 'LU', 'LUX', 442, 'Luxembourg', 0, '352', NULL),
(125, 'MO', 'MO', 'MAC', 446, 'Macao SAR', 0, '853', NULL),
(126, 'MK', 'MK', 'MKD', 807, 'Macedonia', 0, '389', NULL),
(127, 'MG', 'MG', 'MDG', 450, 'Madagascar', 0, '261', NULL),
(128, 'MW', 'MW', 'MWI', 454, 'Malawi', 0, '265', NULL),
(129, 'MY', 'MY', 'MYS', 458, 'Malaysia', 0, '60', NULL),
(130, 'MV', 'MV', 'MDV', 462, 'Maldives', 0, '960', NULL),
(131, 'ML', 'ML', 'MLI', 466, 'Mali', 0, '223', NULL),
(132, 'MT', 'MT', 'MLT', 470, 'Malta', 0, '356', NULL),
(133, 'MH', 'MH', 'MHL', 584, 'Marshall Islands', 0, '692', NULL),
(134, 'MQ', 'MQ', 'MTQ', 474, 'Martinique', 0, '596', NULL),
(135, 'MR', 'MR', 'MRT', 478, 'Mauritania', 0, '222', NULL),
(136, 'MU', 'MU', 'MUS', 480, 'Mauritius', 0, '230', NULL),
(137, 'YT', 'YT', 'MYT', 175, 'Mayotte', 0, '262', NULL),
(138, 'MX', 'MX', 'MEX', 484, 'Mexico', 0, '52', NULL),
(139, 'FM', 'FM', 'FSM', 583, 'Micronesia', 0, '691', NULL),
(140, 'MD', 'MD', 'MDA', 498, 'Moldova', 0, '373', NULL),
(141, 'MC', 'MC', 'MCO', 492, 'Monaco', 0, '377', NULL),
(142, 'MN', 'MN', 'MNG', 496, 'Mongolia', 0, '976', NULL),
(143, 'MS', 'MS', 'MSR', 500, 'Montserrat', 0, '1-664', NULL),
(144, 'MA', 'MA', 'MAR', 504, 'Morocco', 0, '212', NULL),
(145, 'MZ', 'MZ', 'MOZ', 508, 'Mozambique', 0, '258', NULL),
(146, 'MM', 'MM', 'MMR', 104, 'Myanmar', 0, '95', NULL),
(147, 'NA', 'NA', 'NAM', 516, 'Namibia', 0, '264', NULL),
(148, 'NR', 'NR', 'NRU', 520, 'Nauru', 0, '674', NULL),
(149, 'NP', 'NP', 'NPL', 524, 'Nepal', 0, '977', NULL),
(150, 'NL', 'NL', 'NLD', 528, 'Netherlands', 0, '31', NULL),
(151, 'AN', 'AN', 'ANT', 127, 'Netherlands Antilles', 0, '599', NULL),
(152, 'NC', 'NC', 'NCL', 540, 'New Caledonia', 0, '687', NULL),
(153, 'NZ', 'NZ', 'NZL', 554, 'New Zealand', 0, '64', NULL),
(154, 'NI', 'NI', 'NIC', 558, 'Nicaragua', 0, '505', NULL),
(155, 'NE', 'NE', 'NER', 562, 'Niger', 0, '227', NULL),
(156, 'NG', 'NG', 'NGA', 566, 'Nigeria', 0, '234', NULL),
(157, 'NU', 'NU', 'NIU', 570, 'Niue', 0, '683', NULL),
(158, 'NF', 'NF', 'NFK', 574, 'Norfolk Island', 0, '672', NULL),
(159, 'KP', 'KP', 'PRK', 408, 'North Korea', 0, '850', NULL),
(160, 'MP', 'MP', 'MNP', 580, 'Northern Mariana Islands', 0, '1-670', NULL),
(161, 'NO', 'NO', 'NOR', 578, 'Norway', 0, '47', NULL),
(162, 'OM', 'OM', 'OMN', 512, 'Oman', 0, '968', NULL),
(163, 'PK', 'PK', 'PAK', 586, 'Pakistan', 0, '92', NULL),
(164, 'PW', 'PW', 'PLW', 585, 'Palau', 0, '680', NULL),
(165, 'PA', 'PA', 'PAN', 591, 'Panama', 0, '507', NULL),
(166, 'PG', 'PG', 'PNG', 598, 'Papua New Guinea', 0, '675', NULL),
(167, 'PY', 'PY', 'PRY', 600, 'Paraguay', 0, '595', NULL),
(168, 'PE', 'PE', 'PER', 604, 'Peru', 0, '51', NULL),
(169, 'PH', 'PH', 'PHL', 608, 'Philippines', 0, '63', NULL),
(170, 'PN', 'PN', 'PCN', 612, 'Pitcairn Islands', 0, '64', NULL),
(171, 'PL', 'PL', 'POL', 616, 'Poland', 0, '48', NULL),
(172, 'PT', 'PT', 'PRT', 620, 'Portugal', 0, '351', NULL),
(173, 'PR', 'PR', 'PRI', 630, 'Puerto Rico', 0, '1-787,1-939', NULL),
(174, 'QA', 'QA', 'QAT', 634, 'Qatar', 0, '974', NULL),
(175, 'RE', 'RE', 'REU', 638, 'Reunion', 0, '262', NULL),
(176, 'RO', 'RO', 'ROU', 642, 'Romania', 0, '40', NULL),
(177, 'RU', 'RU', 'RUS', 643, 'Russia', 0, '7', NULL),
(178, 'RW', 'RW', 'RWA', 646, 'Rwanda', 0, '250', NULL),
(179, 'WS', 'WS', 'WSM', 882, 'Samoa', 0, '685', NULL),
(180, 'SM', 'SM', 'SMR', 674, 'San Marino', 0, '378', NULL),
(181, 'ST', 'ST', 'STP', 678, 'Sao Tome and Principe', 0, '239', NULL),
(182, 'SA', 'SA', 'SAU', 682, 'Saudi Arabia', 0, '966', NULL),
(183, 'SN', 'SN', 'SEN', 686, 'Senegal', 0, '221', NULL),
(184, 'ME', 'ME', 'MNE', 499, 'Montenegro', 0, '382', NULL),
(185, 'SC', 'SC', 'SYC', 690, 'Seychelles', 0, '248', NULL),
(186, 'SL', 'SL', 'SLE', 694, 'Sierra Leone', 0, '232', NULL),
(187, 'SG', 'SG', 'SGP', 702, 'Singapore', 0, '65', NULL),
(188, 'SK', 'SK', 'SVK', 703, 'Slovakia', 0, '421', NULL),
(189, 'SI', 'SI', 'SVN', 705, 'Slovenia', 0, '386', NULL),
(190, 'SB', 'SB', 'SLB', 90, 'Solomon Islands', 0, '677', NULL),
(191, 'SO', 'SO', 'SOM', 706, 'Somalia', 0, '252', NULL),
(192, 'ZA', 'ZA', 'ZAF', 710, 'South Africa', 0, '27', NULL),
(193, 'GS', 'GS', 'SGS', 239, 'South Georgia and the South Sandwich Islands', 0, '500', NULL),
(194, 'ES', 'ES', 'ESP', 724, 'Spain', 0, '34', NULL),
(195, 'LK', 'LK', 'LKA', 144, 'Sri Lanka', 0, '94', NULL),
(196, 'SH', 'SH', 'SHN', 654, 'St. Helena', 0, '290', NULL),
(197, 'KN', 'KN', 'KNA', 659, 'St. Kitts and Nevis', 0, '1-869', NULL),
(198, 'LC', 'LC', 'LCA', 662, 'St. Lucia', 0, '1-758', NULL),
(199, 'PM', 'PM', 'SPM', 666, 'St. Pierre and Miquelon', 0, '508', NULL),
(200, 'VC', 'VC', 'VCT', 670, 'St. Vincent and the Grenadines', 0, '1-784', NULL),
(201, 'SD', 'SD', 'SDN', 729, 'Sudan', 0, '249', NULL),
(202, 'SR', 'SR', 'SUR', 740, 'Suriname', 0, '597', NULL),
(203, 'SJ', 'SJ', 'SJM', 744, 'Svalbard and Jan Mayen', 0, '47', NULL),
(204, 'SZ', 'SZ', 'SWZ', 748, 'Swaziland', 0, '268', NULL),
(205, 'SE', 'SE', 'SWE', 752, 'Sweden', 0, '46', NULL),
(206, 'CH', 'CH', 'CHE', 756, 'Switzerland', 0, '41', NULL),
(207, 'SY', 'SY', 'SYR', 760, 'Syria', 0, '963', NULL),
(208, 'TW', 'TW', 'TWN', 158, 'Taiwan', 0, '886', NULL),
(209, 'TJ', 'TJ', 'TJK', 762, 'Tajikistan', 0, '992', NULL),
(210, 'TZ', 'TZ', 'TZA', 834, 'Tanzania', 0, '255', NULL),
(211, 'TH', 'TH', 'THA', 764, 'Thailand', 0, '66', NULL),
(212, 'TG', 'TG', 'TGO', 768, 'Togo', 0, '228', NULL),
(213, 'TK', 'TK', 'TKL', 772, 'Tokelau', 0, '690', NULL),
(214, 'TO', 'TO', 'TON', 776, 'Tonga', 0, '676', NULL),
(215, 'TT', 'TT', 'TTO', 780, 'Trinidad and Tobago', 0, '1-868', NULL),
(216, 'TN', 'TN', 'TUN', 788, 'Tunisia', 0, '216', NULL),
(217, 'TR', 'TR', 'TUR', 792, 'Turkey', 0, '90', NULL),
(218, 'TM', 'TM', 'TKM', 795, 'Turkmenistan', 0, '993', NULL),
(219, 'TC', 'TC', 'TCA', 796, 'Turks and Caicos Islands', 0, '1-649', NULL),
(220, 'TV', 'TV', 'TUV', 798, 'Tuvalu', 0, '688', NULL),
(221, 'UG', 'UG', 'UGA', 800, 'Uganda', 0, '256', NULL),
(222, 'UA', 'UA', 'UKR', 804, 'Ukraine', 0, '380', NULL),
(223, 'AE', 'AE', 'ARE', 784, 'United Arab Emirates', 0, '971', NULL),
(224, 'GB', 'GB', 'GBR', 826, 'United Kingdom', 0, '44', NULL),
(225, 'UM', 'UM', 'UMI', 581, 'United States Minor Outlying Islands', 0, NULL, NULL),
(226, 'UY', 'UY', 'URY', 858, 'Uruguay', 0, '598', NULL),
(227, 'UZ', 'UZ', 'UZB', 860, 'Uzbekistan', 0, '998', NULL),
(228, 'VU', 'VU', 'VUT', 548, 'Vanuatu', 0, '678', NULL),
(229, 'VA', 'VA', 'VAT', 336, 'Vatican City', 0, '379', NULL),
(230, 'VE', 'VE', 'VEN', 862, 'Venezuela', 0, '58', NULL),
(231, 'VN', 'VN', 'VNM', 704, 'Viet Nam', 0, '84', NULL),
(232, 'VG', 'VG', 'VGB', 92, 'Virgin Islands (British)', 0, '1-284', NULL),
(233, 'VI', 'VI', 'VIR', 850, 'Virgin Islands', 0, '1-340', NULL),
(234, 'WF', 'WF', 'WLF', 876, 'Wallis and Futuna', 0, '681', NULL),
(235, 'YE', 'YE', 'YEM', 887, 'Yemen', 0, '967', NULL),
(236, 'ZM', 'ZM', 'ZMB', 894, 'Zambia', 0, '260', NULL),
(237, 'ZW', 'ZW', 'ZWE', 716, 'Zimbabwe', 0, '263', NULL),
(238, 'AX', 'AX', 'ALA', 248, 'Åland Islands', 0, '358', NULL),
(239, 'BQ', 'BQ', 'BES', 535, 'Bonaire, Sint Eustatius and Saba', 0, '599', NULL),
(240, 'CW', 'CW', 'CUW', 531, 'Curaçao', 0, '599', NULL),
(241, 'GG', 'GG', 'GGY', 831, 'Guernsey', 0, '44-1481', NULL),
(242, 'IM', 'IM', 'IMN', 833, 'Isle of Man', 0, '44-1624', NULL),
(243, 'JE', 'JE', 'JEY', 832, 'Jersey', 0, '44-1534', NULL),
(245, 'PS', 'PS', 'PSE', 275, 'Palestinian Territory, Occupied', 0, '970', NULL),
(246, 'BL', 'BL', 'BLM', 652, 'Saint Barthélemy', 0, '590', NULL),
(247, 'MF', 'MF', 'MAF', 663, 'Saint Martin (French part)', 0, '590', NULL),
(248, 'RS', 'RS', 'SRB', 688, 'Serbia', 0, '381', NULL),
(249, 'SX', 'SX', 'SXM', 534, 'Sint Maarten (Dutch part)', 0, '1-721', NULL),
(250, 'SS', 'SS', 'SSD', 728, 'South Sudan', 0, '211', NULL),
(251, 'EH', 'EH', 'ESH', 732, 'Western Sahara', 0, '212', NULL)
ON DUPLICATE KEY UPDATE alpha2 = VALUES(alpha2), alpha3 = VALUES(alpha3), `numeric` = VALUES(`numeric`);
