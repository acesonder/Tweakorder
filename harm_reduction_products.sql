-- Comprehensive Harm Reduction Product List
-- This file populates the products table with a complete harm reduction supply catalog
-- All fields are filled including custom icons for each product

USE tweakorder;

-- Ensure products table has all required columns for compatibility with older schemas
-- Add category column if it doesn't exist
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS category VARCHAR(100) 
AFTER background_color;

-- Add sku column if it doesn't exist
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS sku VARCHAR(100) 
AFTER category;

-- Add is_favorite column if it doesn't exist
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS is_favorite BOOLEAN DEFAULT 0 
AFTER sku;

-- Clear existing sample products if needed (optional, comment out if you want to keep existing products)
-- DELETE FROM products WHERE category = 'Harm Reduction';

-- Insert comprehensive harm reduction products
INSERT INTO products (name, description, image, inventory, background_color, category, sku) VALUES

-- Naloxone & Overdose Prevention
('Naloxone Nasal Spray Kit', 'Emergency overdose reversal medication - nasal spray format with instructions. Life-saving medication for opioid overdoses. Contains 2 doses.', 'harm-reduction-icons/naloxone.svg', 100, 'gradient-1', 'Harm Reduction', 'HR-NAL-001'),
('Naloxone Injectable Kit', 'Emergency overdose reversal medication - injectable format with syringes and instructions. For intramuscular or subcutaneous administration.', 'harm-reduction-icons/naloxone.svg', 75, 'gradient-1', 'Harm Reduction', 'HR-NAL-002'),
('Overdose Response Training Card', 'Step-by-step guide for recognizing and responding to opioid overdoses. Includes emergency contact information.', 'harm-reduction-icons/resource-card.svg', 200, 'gradient-2', 'Harm Reduction', 'HR-EDU-001'),

-- Injection Supplies
('Sterile Syringe - 1mL', 'Single-use sterile syringe with safety cap, 1mL capacity. Reduces risk of disease transmission and infection.', 'harm-reduction-icons/syringe.svg', 500, 'gradient-3', 'Harm Reduction', 'HR-SYR-001'),
('Sterile Syringe - 3mL', 'Single-use sterile syringe with safety cap, 3mL capacity. Larger capacity for various applications.', 'harm-reduction-icons/syringe.svg', 300, 'gradient-3', 'Harm Reduction', 'HR-SYR-002'),
('Insulin Syringe 0.5mL', 'Ultra-fine insulin syringe with fixed needle, 0.5mL capacity. Ideal for smaller volume injections.', 'harm-reduction-icons/syringe.svg', 400, 'gradient-3', 'Harm Reduction', 'HR-SYR-003'),
('Sterile Needles - 25G', '25 gauge sterile hypodermic needles. Compatible with standard syringes. Individually packaged.', 'harm-reduction-icons/syringe.svg', 600, 'gradient-4', 'Harm Reduction', 'HR-NDL-001'),
('Sterile Needles - 27G', '27 gauge sterile hypodermic needles. Finer gauge for sensitive injection sites.', 'harm-reduction-icons/syringe.svg', 500, 'gradient-4', 'Harm Reduction', 'HR-NDL-002'),
('Cookers/Spoons (Sterile)', 'Sterile cookers for preparing substances. Heat-safe and single-use to prevent contamination.', 'harm-reduction-icons/cooker.svg', 400, 'gradient-5', 'Harm Reduction', 'HR-COK-001'),
('Cotton Filters', 'Sterile cotton filters for preparing injections. Removes particulate matter and reduces harm.', 'harm-reduction-icons/cotton-filters.svg', 500, 'gradient-6', 'Harm Reduction', 'HR-FIL-001'),
('Alcohol Prep Pads (100 pack)', 'Sterile alcohol swabs for cleaning injection sites. Reduces risk of skin infections and abscesses.', 'harm-reduction-icons/alcohol-swabs.svg', 300, 'gradient-7', 'Harm Reduction', 'HR-ALC-001'),
('Tourniquets', 'Latex-free elastic tourniquets for vein identification. Single-use to prevent disease transmission.', 'harm-reduction-icons/tourniquet.svg', 250, 'gradient-8', 'Harm Reduction', 'HR-TRN-001'),
('Sharps Container (1L)', 'Puncture-resistant biohazard sharps disposal container, 1 liter capacity. Safe needle disposal essential.', 'harm-reduction-icons/sharps-container.svg', 150, 'gradient-9', 'Harm Reduction', 'HR-SHP-001'),
('Sharps Container (2L)', 'Large puncture-resistant sharps container, 2 liter capacity. For higher volume disposal needs.', 'harm-reduction-icons/sharps-container.svg', 100, 'gradient-9', 'Harm Reduction', 'HR-SHP-002'),
('Sterile Water Vials (10mL)', 'Sterile water for injection preparation. Reduces risk of bacterial infection from contaminated water sources.', 'harm-reduction-icons/water-bottle.svg', 400, 'gradient-10', 'Harm Reduction', 'HR-WAT-001'),

-- Safer Sex & Sexual Health
('Condoms - Standard (10 pack)', 'Latex condoms for safer sex practices. Prevents STI transmission and unplanned pregnancy.', 'harm-reduction-icons/condoms.svg', 500, 'gradient-11', 'Harm Reduction', 'HR-CON-001'),
('Condoms - Large (10 pack)', 'Larger size latex condoms. Better fit reduces breakage risk.', 'harm-reduction-icons/condoms.svg', 300, 'gradient-11', 'Harm Reduction', 'HR-CON-002'),
('Non-Latex Condoms (10 pack)', 'Polyurethane condoms for latex allergies. Effective STI and pregnancy prevention.', 'harm-reduction-icons/condoms.svg', 250, 'gradient-11', 'Harm Reduction', 'HR-CON-003'),
('Internal Condoms (5 pack)', 'Internal (female) condoms for safer sex. Provides user control over protection.', 'harm-reduction-icons/condoms.svg', 200, 'gradient-12', 'Harm Reduction', 'HR-CON-004'),
('Lubricant Packets', 'Water-based personal lubricant sachets. Reduces friction and tearing during intercourse.', 'harm-reduction-icons/lubricant.svg', 600, 'gradient-13', 'Harm Reduction', 'HR-LUB-001'),
('Dental Dams', 'Latex barriers for oral sex protection. Prevents STI transmission during oral-vaginal/anal contact.', 'harm-reduction-icons/condoms.svg', 200, 'gradient-14', 'Harm Reduction', 'HR-DEN-001'),

-- Testing Supplies
('Fentanyl Test Strips (10 pack)', 'Test strips to detect fentanyl in substances. Critical for preventing accidental overdoses.', 'harm-reduction-icons/fentanyl-test.svg', 300, 'gradient-15', 'Harm Reduction', 'HR-TST-001'),
('Drug Checking Test Strips', 'Multi-drug test strips for substance checking. Identifies adulterants and unexpected substances.', 'harm-reduction-icons/test-strips.svg', 250, 'gradient-1', 'Harm Reduction', 'HR-TST-002'),
('Xylazine Test Strips', 'Test strips specifically for detecting xylazine contamination. Emerging threat in drug supply.', 'harm-reduction-icons/test-strips.svg', 200, 'gradient-2', 'Harm Reduction', 'HR-TST-003'),
('HIV Self-Test Kit', 'At-home HIV testing kit with instructions. Results in 20 minutes, encourages regular testing.', 'harm-reduction-icons/test-strips.svg', 100, 'gradient-3', 'Harm Reduction', 'HR-TST-004'),
('Hepatitis C Test Kit', 'Rapid HCV antibody test kit. Early detection improves treatment outcomes.', 'harm-reduction-icons/test-strips.svg', 100, 'gradient-4', 'Harm Reduction', 'HR-TST-005'),

-- Smoking/Inhalation Supplies
('Safe Smoking Kit - Complete', 'Comprehensive safer smoking supplies including pipes, screens, mouthpieces, and lip balm.', 'harm-reduction-icons/safe-smoke-kit.svg', 200, 'gradient-5', 'Harm Reduction', 'HR-SMK-001'),
('Glass Stems/Pipes', 'Pyrex glass stems for safer smoking. Reduces cuts and burns from makeshift pipes.', 'harm-reduction-icons/safe-smoke-kit.svg', 300, 'gradient-6', 'Harm Reduction', 'HR-SMK-002'),
('Metal Screens (50 pack)', 'Stainless steel pipe screens. Prevents inhalation of ash and debris.', 'harm-reduction-icons/safe-smoke-kit.svg', 400, 'gradient-7', 'Harm Reduction', 'HR-SMK-003'),
('Mouthpieces (Rubber)', 'Individual rubber mouthpieces for pipes. Prevents lip burns and oral infections.', 'harm-reduction-icons/mouthpiece.svg', 500, 'gradient-8', 'Harm Reduction', 'HR-SMK-004'),
('Push Sticks/Choppers', 'Wooden push sticks for safer pipe use. Reduces finger burns.', 'harm-reduction-icons/safe-smoke-kit.svg', 400, 'gradient-9', 'Harm Reduction', 'HR-SMK-005'),
('Brass Screens (100 pack)', 'Brass pipe screens alternative. Durable and effective filtering.', 'harm-reduction-icons/safe-smoke-kit.svg', 350, 'gradient-10', 'Harm Reduction', 'HR-SMK-006'),

-- Wound Care & First Aid
('Wound Care Kit - Basic', 'Basic wound care supplies: bandages, gauze, antibiotic ointment, medical tape. Treats injection site infections.', 'harm-reduction-icons/wound-care.svg', 200, 'gradient-11', 'Harm Reduction', 'HR-WND-001'),
('Wound Care Kit - Advanced', 'Advanced wound care with irrigation supplies, sterile dressings, and abscess care items.', 'harm-reduction-icons/wound-care.svg', 150, 'gradient-11', 'Harm Reduction', 'HR-WND-002'),
('Antibiotic Ointment Packets', 'Single-use antibiotic ointment for minor cuts and injection sites. Prevents infection.', 'harm-reduction-icons/bandages.svg', 500, 'gradient-12', 'Harm Reduction', 'HR-WND-003'),
('Sterile Gauze Pads (4x4)', 'Individually wrapped sterile gauze pads. For wound cleaning and dressing.', 'harm-reduction-icons/gauze.svg', 400, 'gradient-13', 'Harm Reduction', 'HR-WND-004'),
('Medical Tape Roll', 'Hypoallergenic medical adhesive tape. Secures dressings and bandages.', 'harm-reduction-icons/bandages.svg', 300, 'gradient-14', 'Harm Reduction', 'HR-WND-005'),
('Adhesive Bandages (Variety)', 'Assorted adhesive bandages in multiple sizes. For minor cuts and abrasions.', 'harm-reduction-icons/bandages.svg', 600, 'gradient-15', 'Harm Reduction', 'HR-WND-006'),
('Burn Gel Packets', 'Single-use burn relief gel. For thermal burns from pipes or cookers.', 'harm-reduction-icons/first-aid-kit.svg', 250, 'gradient-1', 'Harm Reduction', 'HR-WND-007'),

-- Personal Protective Equipment
('Nitrile Gloves - Small (100)', 'Latex-free nitrile gloves, small size. Essential for safer assistance and wound care.', 'harm-reduction-icons/gloves.svg', 300, 'gradient-2', 'Harm Reduction', 'HR-PPE-001'),
('Nitrile Gloves - Medium (100)', 'Latex-free nitrile gloves, medium size. Most commonly requested size.', 'harm-reduction-icons/gloves.svg', 400, 'gradient-2', 'Harm Reduction', 'HR-PPE-002'),
('Nitrile Gloves - Large (100)', 'Latex-free nitrile gloves, large size. For larger hands.', 'harm-reduction-icons/gloves.svg', 300, 'gradient-2', 'Harm Reduction', 'HR-PPE-003'),
('Disposable Face Masks (50)', '3-layer disposable face masks. Prevents respiratory disease transmission.', 'harm-reduction-icons/face-mask.svg', 500, 'gradient-3', 'Harm Reduction', 'HR-PPE-004'),
('Hand Sanitizer (2oz)', 'Alcohol-based hand sanitizer, 2oz bottle. Essential hygiene when handwashing unavailable.', 'harm-reduction-icons/hand-sanitizer.svg', 400, 'gradient-4', 'Harm Reduction', 'HR-PPE-005'),
('Hand Sanitizer (8oz)', 'Alcohol-based hand sanitizer, larger 8oz bottle. For frequent use.', 'harm-reduction-icons/hand-sanitizer.svg', 200, 'gradient-4', 'Harm Reduction', 'HR-PPE-006'),

-- Vitamin & Nutrition Support
('Vitamin C Tablets (100mg)', 'Ascorbic acid tablets for vein health. Reduces vein damage from acidic substances.', 'harm-reduction-icons/vitamin-c.svg', 300, 'gradient-5', 'Harm Reduction', 'HR-VIT-001'),
('Multivitamin Packets', 'Daily multivitamin packets. Supports overall health and immune function.', 'harm-reduction-icons/vitamin-c.svg', 250, 'gradient-6', 'Harm Reduction', 'HR-VIT-002'),
('Electrolyte Powder Packets', 'Oral rehydration salts/electrolyte powder. Prevents dehydration.', 'harm-reduction-icons/water-bottle.svg', 400, 'gradient-7', 'Harm Reduction', 'HR-VIT-003'),

-- Disposal & Safety
('Biohazard Disposal Bags', 'Red biohazard bags for safe disposal of used supplies. Prevents accidental needle sticks.', 'harm-reduction-icons/biohazard-bag.svg', 300, 'gradient-8', 'Harm Reduction', 'HR-DIS-001'),
('Needle Clippers', 'Portable needle destruction devices. Safely removes needles from syringes before disposal.', 'harm-reduction-icons/sharps-container.svg', 150, 'gradient-9', 'Harm Reduction', 'HR-DIS-002'),

-- Educational Materials & Resources
('Safer Injection Guide', 'Comprehensive safer injection practices pamphlet. Illustrated step-by-step instructions.', 'harm-reduction-icons/safer-use-info.svg', 500, 'gradient-10', 'Harm Reduction', 'HR-EDU-002'),
('Safer Smoking Guide', 'Harm reduction guide for smoking substances. Techniques to reduce respiratory harm.', 'harm-reduction-icons/safer-use-info.svg', 400, 'gradient-11', 'Harm Reduction', 'HR-EDU-003'),
('Safer Sex Guide', 'Sexual health and STI prevention guide. Includes local testing resources.', 'harm-reduction-icons/safer-use-info.svg', 400, 'gradient-12', 'Harm Reduction', 'HR-EDU-004'),
('Overdose Prevention Card', 'Wallet-sized overdose recognition and response card. Quick reference for emergencies.', 'harm-reduction-icons/resource-card.svg', 600, 'gradient-13', 'Harm Reduction', 'HR-EDU-005'),
('Local Resources Card', 'Directory of local harm reduction, treatment, and support services. Updated contact information.', 'harm-reduction-icons/resource-card.svg', 500, 'gradient-14', 'Harm Reduction', 'HR-EDU-006'),
('Vein Care Guide', 'Guide to maintaining vein health and rotating injection sites. Prevents collapsed veins.', 'harm-reduction-icons/safer-use-info.svg', 400, 'gradient-15', 'Harm Reduction', 'HR-EDU-007'),
('Abscess Prevention Card', 'Information on preventing and recognizing skin infections. When to seek medical care.', 'harm-reduction-icons/resource-card.svg', 400, 'gradient-1', 'Harm Reduction', 'HR-EDU-008'),

-- Miscellaneous Harm Reduction Supplies
('Lip Balm (Medicated)', 'Medicated lip balm. Prevents and treats cracked lips from pipe use.', 'harm-reduction-icons/safe-smoke-kit.svg', 500, 'gradient-2', 'Harm Reduction', 'HR-MSC-001'),
('Tissues (Travel Pack)', 'Travel-size tissue packs. For personal hygiene and nosebleeds.', 'harm-reduction-icons/resource-card.svg', 400, 'gradient-3', 'Harm Reduction', 'HR-MSC-002'),
('Menstrual Products (Pads)', 'Sanitary pads, variety pack. Menstrual hygiene supplies.', 'harm-reduction-icons/resource-card.svg', 300, 'gradient-4', 'Harm Reduction', 'HR-MSC-003'),
('Menstrual Products (Tampons)', 'Tampons, variety pack. Alternative menstrual hygiene option.', 'harm-reduction-icons/resource-card.svg', 300, 'gradient-5', 'Harm Reduction', 'HR-MSC-004'),
('Pregnancy Tests (2 pack)', 'Home pregnancy test kits. Early detection for informed choices.', 'harm-reduction-icons/test-strips.svg', 200, 'gradient-6', 'Harm Reduction', 'HR-MSC-005'),
('Emergency Contraception', 'Emergency contraceptive pills (Plan B or equivalent). Time-sensitive pregnancy prevention.', 'harm-reduction-icons/resource-card.svg', 100, 'gradient-7', 'Harm Reduction', 'HR-MSC-006'),
('Sunscreen (SPF 30)', 'Broad-spectrum sunscreen. Skin protection for outdoor living situations.', 'harm-reduction-icons/resource-card.svg', 250, 'gradient-8', 'Harm Reduction', 'HR-MSC-007'),
('Insect Repellent', 'DEET-based insect repellent. Prevents mosquito and tick-borne illness.', 'harm-reduction-icons/resource-card.svg', 200, 'gradient-9', 'Harm Reduction', 'HR-MSC-008'),

-- Kits & Bundles
('Starter Harm Reduction Kit', 'Comprehensive starter kit: syringes, cookers, alcohol swabs, sharps container, naloxone info, resource cards.', 'harm-reduction-icons/first-aid-kit.svg', 150, 'gradient-10', 'Harm Reduction', 'HR-KIT-001'),
('Injection Supplies Bundle', 'Complete injection supplies: 10 syringes, cookers, cotton, alcohol swabs, sterile water, tourniquets, bandages.', 'harm-reduction-icons/first-aid-kit.svg', 200, 'gradient-11', 'Harm Reduction', 'HR-KIT-002'),
('Safer Sex Bundle', 'Safer sex kit: condoms (variety), lubricant, dental dams, educational materials.', 'harm-reduction-icons/first-aid-kit.svg', 250, 'gradient-12', 'Harm Reduction', 'HR-KIT-003'),
('Smoking Supplies Bundle', 'Complete safer smoking kit: stems, screens, mouthpieces, push sticks, lip balm, education card.', 'harm-reduction-icons/first-aid-kit.svg', 200, 'gradient-13', 'Harm Reduction', 'HR-KIT-004'),
('Personal Care Bundle', 'Hygiene essentials: hand sanitizer, tissues, bandages, lip balm, sunscreen, menstrual products.', 'harm-reduction-icons/first-aid-kit.svg', 180, 'gradient-14', 'Harm Reduction', 'HR-KIT-005'),
('Overdose Response Kit', 'Emergency overdose kit: naloxone (2 doses), rescue breathing barrier, gloves, instruction card, emergency contacts.', 'harm-reduction-icons/first-aid-kit.svg', 100, 'gradient-15', 'Harm Reduction', 'HR-KIT-006');

-- Update inventory counts based on typical demand patterns
-- High-demand items already set to higher inventory
-- Educational materials and disposables have highest inventory
-- Specialized medical items (naloxone, test kits) have moderate inventory
