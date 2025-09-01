-- Drop existing organizers table and recreate with correct structure
DROP TABLE IF EXISTS support_tickets;
DROP TABLE IF EXISTS analytics_b2b;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS organizer_events;
DROP TABLE IF EXISTS partnerships;
DROP TABLE IF EXISTS organizers;

-- Create organizers table with correct fields
CREATE TABLE organizers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    postal_code VARCHAR(20),
    website VARCHAR(255),
    description TEXT,
    logo_url VARCHAR(500),
    organization_type ENUM('company', 'association', 'public', 'freelance') DEFAULT 'company',
    employee_count INT DEFAULT 1,
    annual_events INT DEFAULT 0,
    subscription_type ENUM('free', 'basic', 'premium') DEFAULT 'free',
    subscription_plan ENUM('free', 'starter', 'pro', 'enterprise') DEFAULT 'free',
    subscription_expires DATE,
    commission_rate DECIMAL(5,2) DEFAULT 10.00,
    is_active BOOLEAN DEFAULT TRUE,
    is_verified BOOLEAN DEFAULT FALSE,
    is_premium BOOLEAN DEFAULT FALSE,
    rating DECIMAL(3,2) DEFAULT 0,
    total_revenue DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_active (is_active),
    INDEX idx_verified (is_verified),
    INDEX idx_subscription (subscription_plan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert test organizers
INSERT INTO organizers (
    name, company_name, contact_name, email, password, phone,
    address, city, postal_code, website, description,
    organization_type, employee_count, annual_events,
    subscription_type, subscription_plan, subscription_expires,
    is_active, is_verified, is_premium
) VALUES
(
    'Live Nation France',
    'Live Nation France',
    'Jean-Marc Dubois',
    'contact@livenation.fr',
    '$2y$10$YmG8w.C2mQXKlvLp7RP8w.uxzLlYxL2wJFfHxhNOyg0BKJLXvUqCe', -- password: organizer123
    '01 42 86 12 34',
    '15 rue de la Culture',
    'Paris',
    '75008',
    'https://www.livenationfrance.fr',
    'Leader dans l''organisation d''événements culturels.',
    'company',
    250,
    150,
    'premium',
    'enterprise',
    DATE_ADD(NOW(), INTERVAL 365 DAY),
    1, 1, 1
),
(
    'Jazz à Paris',
    'Association Jazz à Paris',
    'Marie Laurent', 
    'marie@jazzparis.org',
    '$2y$10$YmG8w.C2mQXKlvLp7RP8w.uxzLlYxL2wJFfHxhNOyg0BKJLXvUqCe',
    '01 43 55 67 89',
    '42 rue du Jazz',
    'Paris',
    '75011',
    'https://www.jazzparis.org',
    'Association dédiée à la promotion du jazz à Paris.',
    'association',
    12,
    50,
    'basic',
    'starter',
    DATE_ADD(NOW(), INTERVAL 180 DAY),
    1, 1, 0
),
(
    'Indie Events',
    'Indie Events',
    'Camille Roy',
    'hello@indie-events.fr',
    '$2y$10$YmG8w.C2mQXKlvLp7RP8w.uxzLlYxL2wJFfHxhNOyg0BKJLXvUqCe',
    '06 98 76 54 32',
    '5 rue des Artistes',
    'Paris',
    '75003',
    'https://www.indie-events.fr',
    'Événements culturels indépendants et alternatifs.',
    'freelance',
    1,
    20,
    'free',
    'free',
    NULL,
    1, 0, 0
);

-- Create partnerships table
CREATE TABLE partnerships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partner_name VARCHAR(255) NOT NULL,
    partner_type ENUM('sponsor', 'media', 'venue', 'service', 'technology') DEFAULT 'sponsor',
    contact_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    description TEXT,
    logo_url VARCHAR(500),
    website VARCHAR(255),
    partnership_level ENUM('bronze', 'silver', 'gold', 'platinum') DEFAULT 'bronze',
    contract_value DECIMAL(10,2) DEFAULT 0,
    contract_start DATE,
    contract_end DATE,
    benefits JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_partner_type (partner_type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create organizer_events table
CREATE TABLE organizer_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    event_id INT NOT NULL,
    revenue_share DECIMAL(5,2) DEFAULT 70.00,
    tickets_sold INT DEFAULT 0,
    total_revenue DECIMAL(10,2) DEFAULT 0,
    status ENUM('draft', 'published', 'cancelled', 'completed') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES organizers(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_organizer_event (organizer_id, event_id),
    INDEX idx_organizer (organizer_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create invoices table
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    organizer_id INT NOT NULL,
    event_id INT,
    amount DECIMAL(10,2) NOT NULL,
    commission_amount DECIMAL(10,2) DEFAULT 0,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    due_date DATE,
    paid_date DATE,
    payment_method VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES organizers(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    INDEX idx_organizer_invoices (organizer_id, status),
    INDEX idx_invoice_number (invoice_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create analytics table
CREATE TABLE analytics_b2b (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    metric_date DATE NOT NULL,
    page_views INT DEFAULT 0,
    unique_visitors INT DEFAULT 0,
    conversion_rate DECIMAL(5,2) DEFAULT 0,
    tickets_sold INT DEFAULT 0,
    revenue DECIMAL(10,2) DEFAULT 0,
    avg_ticket_price DECIMAL(8,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES organizers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_daily_metric (organizer_id, metric_date),
    INDEX idx_organizer_date (organizer_id, metric_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create support tickets table
CREATE TABLE support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    category VARCHAR(50),
    assigned_to VARCHAR(100),
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES organizers(id) ON DELETE CASCADE,
    INDEX idx_organizer_tickets (organizer_id, status),
    INDEX idx_priority (priority, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;