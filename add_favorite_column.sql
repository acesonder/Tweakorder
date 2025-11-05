-- Migration: Add is_favorite column to products table
-- Run this to update existing database

USE tweakorder;

-- Add is_favorite column if it doesn't exist
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS is_favorite BOOLEAN DEFAULT 0 
AFTER sku;
