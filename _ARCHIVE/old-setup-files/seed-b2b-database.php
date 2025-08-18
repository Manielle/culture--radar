<?php
/**
 * B2B Database Seeder - Create Mock Data for Business/Organizers
 * Run this script to populate B2B data
 */

header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

echo "<pre>";
echo "====================================\n";
echo "   Culture Radar B2B Data Seeder   \n";
echo "====================================\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Start transaction
    $pdo->beginTransaction();
    
    // 1. CREATE ORGANIZERS TABLE
    echo "1. Creating organizers table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS organizers (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Organizers table created\n";
    
    // 2. INSERT MOCK ORGANIZERS
    echo "\n2. Creating mock organizers...\n";
    
    $organizers = [
        ['Live Nation France', 'Jean-Marc Dubois', 'contact@livenation.fr', '01 42 86 12 34', 'company', 250, 'enterprise', true, true],
        ['Association Jazz à Paris', 'Marie Laurent', 'marie@jazzparis.org', '01 43 55 67 89', 'association', 12, 'pro', true, false],
        ['Mairie de Paris - Culture', 'Sophie Martin', 'culture@paris.fr', '01 42 76 40 40', 'public', 500, 'enterprise', true, true],
        ['Théâtre National', 'Pierre Lefevre', 'direction@theatre-national.fr', '01 44 58 15 15', 'public', 80, 'pro', true, true],
        ['EventProd SARL', 'Lucas Moreau', 'lucas@eventprod.fr', '06 12 34 56 78', 'company', 8, 'starter', false, false],
        ['Musée d\'Art Moderne', 'Claire Dupont', 'contact@mam-paris.fr', '01 53 67 40 00', 'public', 45, 'pro', true, true],
        ['Festival Productions', 'Thomas Bernard', 'info@festprod.com', '01 48 87 25 25', 'company', 35, 'pro', true, false],
        ['Concerts & Co', 'Emma Garcia', 'booking@concertsco.fr', '01 45 62 30 30', 'company', 20, 'starter', false, false],
        ['Opéra de Paris', 'François Petit', 'administration@opera-paris.fr', '01 71 25 24 23', 'public', 300, 'enterprise', true, true],
        ['Indie Events', 'Camille Roy', 'hello@indie-events.fr', '06 98 76 54 32', 'freelance', 1, 'free', false, false],
        ['Grand Palais', 'Alexandre Durand', 'events@grandpalais.fr', '01 44 13 17 17', 'public', 150, 'enterprise', true, true],
        ['StartUp Culture', 'Léa Michel', 'contact@startupculture.io', '06 45 67 89 10', 'company', 5, 'starter', false, false]
    ];
    
    $organizerIds = [];
    $stmt = $pdo->prepare("
        INSERT INTO organizers (
            name, company_name, contact_name, email, password, phone,
            address, city, postal_code, website, description,
            logo_url, organization_type, employee_count, annual_events,
            subscription_type, subscription_plan, subscription_expires, commission_rate,
            is_active, is_verified, is_premium, rating, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($organizers as $org) {
        $password = password_hash('organizer123', PASSWORD_DEFAULT);
        $address = rand(1, 100) . ' rue de la Culture';
        $city = 'Paris';
        $postalCode = '750' . str_pad(rand(1, 20), 2, '0', STR_PAD_LEFT);
        $website = 'https://www.' . str_replace(' ', '', strtolower($org[0])) . '.fr';
        $description = "Leader dans l'organisation d'événements culturels. Spécialisé dans la création d'expériences uniques.";
        $logo = 'https://picsum.photos/seed/' . uniqid() . '/200/200';
        $annualEvents = rand(10, 200);
        $subscriptionExpires = date('Y-m-d', strtotime('+' . rand(30, 365) . ' days'));
        $commission = $org[6] === 'enterprise' ? 5.00 : ($org[6] === 'pro' ? 8.00 : 10.00);
        $rating = rand(35, 50) / 10; // 3.5 to 5.0
        $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(100, 730) . ' days'));
        
        // Map subscription_plan to subscription_type
        $subscriptionType = 'free';
        if ($org[6] === 'enterprise' || $org[6] === 'pro') {
            $subscriptionType = 'premium';
        } elseif ($org[6] === 'starter') {
            $subscriptionType = 'basic';
        }
        
        $stmt->execute([
            $org[0], // name (same as company for now)
            $org[0], // company_name
            $org[1], // contact_name
            $org[2], // email
            $password,
            $org[3], // phone
            $address, $city, $postalCode, $website, $description,
            $logo, $org[4], $org[5], $annualEvents,
            $subscriptionType, // subscription_type for UI
            $org[6], // subscription_plan (original)
            $subscriptionExpires, $commission,
            1, // is_active (all active)
            $org[7] ? 1 : 0, // is_verified
            $org[8] ? 1 : 0, // is_premium
            $rating, $createdAt
        ]);
        
        $organizerIds[] = $pdo->lastInsertId();
        echo "  ✓ Created organizer: {$org[0]}\n";
    }
    
    // 3. CREATE PARTNERSHIPS TABLE
    echo "\n3. Creating partnerships table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS partnerships (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Partnerships table created\n";
    
    // 4. INSERT MOCK PARTNERSHIPS
    echo "\n4. Creating mock partnerships...\n";
    
    $partners = [
        ['BNP Paribas', 'sponsor', 'platinum', 500000, 'Sponsor principal pour les événements culturels majeurs'],
        ['France Télévisions', 'media', 'gold', 200000, 'Partenaire média officiel'],
        ['Le Figaro', 'media', 'silver', 50000, 'Couverture médiatique des événements'],
        ['RATP', 'service', 'gold', 150000, 'Transport officiel des événements'],
        ['Ticketmaster', 'technology', 'platinum', 300000, 'Plateforme de billetterie officielle'],
        ['Carrefour', 'sponsor', 'silver', 75000, 'Sponsor alimentaire'],
        ['Microsoft France', 'technology', 'gold', 180000, 'Solutions technologiques'],
        ['Radio France', 'media', 'gold', 120000, 'Diffusion et promotion radio'],
        ['Airbnb', 'service', 'silver', 80000, 'Hébergement partenaire'],
        ['Société Générale', 'sponsor', 'gold', 250000, 'Services bancaires privilégiés'],
        ['Clear Channel', 'media', 'silver', 60000, 'Affichage urbain'],
        ['Uber', 'service', 'bronze', 30000, 'Transport VIP']
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO partnerships (
            partner_name, partner_type, contact_name, email, phone,
            description, logo_url, website, partnership_level,
            contract_value, contract_start, contract_end, benefits, is_active
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($partners as $partner) {
        $contactName = 'Responsable Partenariats';
        $email = 'partnership@' . str_replace(' ', '', strtolower($partner[0])) . '.fr';
        $phone = '01 ' . rand(40, 59) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99);
        $logo = 'https://picsum.photos/seed/' . uniqid() . '/300/150';
        $website = 'https://www.' . str_replace(' ', '', strtolower($partner[0])) . '.fr';
        $contractStart = date('Y-m-d', strtotime('-' . rand(30, 365) . ' days'));
        $contractEnd = date('Y-m-d', strtotime('+' . rand(30, 730) . ' days'));
        
        $benefits = json_encode([
            'logo_display' => true,
            'booth_space' => $partner[2] !== 'bronze',
            'vip_tickets' => $partner[2] === 'platinum' ? 50 : ($partner[2] === 'gold' ? 20 : 10),
            'newsletter_mention' => true,
            'social_media' => $partner[2] !== 'bronze',
            'speaking_opportunity' => $partner[2] === 'platinum'
        ]);
        
        $stmt->execute([
            $partner[0], $partner[1], $contactName, $email, $phone,
            $partner[4], $logo, $website, $partner[2],
            $partner[3], $contractStart, $contractEnd, $benefits, 1
        ]);
        
        echo "  ✓ Created partnership: {$partner[0]} ({$partner[2]})\n";
    }
    
    // 5. CREATE ORGANIZER EVENTS LINK TABLE
    echo "\n5. Creating organizer_events table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS organizer_events (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Organizer events table created\n";
    
    // Link existing events to organizers
    echo "\n6. Linking events to organizers...\n";
    $eventStmt = $pdo->query("SELECT id FROM events LIMIT 20");
    $eventIds = $eventStmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($eventIds) > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO organizer_events (organizer_id, event_id, revenue_share, tickets_sold, total_revenue, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($eventIds as $eventId) {
            $organizerId = $organizerIds[array_rand($organizerIds)];
            $revenueShare = rand(60, 90);
            $ticketsSold = rand(50, 500);
            $totalRevenue = $ticketsSold * rand(20, 80);
            $status = ['published', 'published', 'published', 'completed'][rand(0, 3)];
            
            $stmt->execute([$organizerId, $eventId, $revenueShare, $ticketsSold, $totalRevenue, $status]);
        }
        echo "  ✓ Linked " . count($eventIds) . " events to organizers\n";
    }
    
    // 6. CREATE INVOICES TABLE
    echo "\n7. Creating invoices table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS invoices (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Invoices table created\n";
    
    // 7. CREATE MOCK INVOICES
    echo "\n8. Creating mock invoices...\n";
    $stmt = $pdo->prepare("
        INSERT INTO invoices (
            invoice_number, organizer_id, event_id, amount, commission_amount,
            status, due_date, paid_date, payment_method, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $invoiceCount = 0;
    foreach ($organizerIds as $organizerId) {
        $numInvoices = rand(2, 8);
        for ($i = 0; $i < $numInvoices; $i++) {
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(++$invoiceCount, 5, '0', STR_PAD_LEFT);
            $eventId = count($eventIds) > 0 ? $eventIds[array_rand($eventIds)] : null;
            $amount = rand(500, 10000);
            $commission = $amount * 0.1;
            $status = ['paid', 'paid', 'paid', 'sent', 'overdue'][rand(0, 4)];
            $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 180) . ' days'));
            $dueDate = date('Y-m-d', strtotime($createdAt . ' +30 days'));
            $paidDate = $status === 'paid' ? date('Y-m-d', strtotime($dueDate . ' -' . rand(1, 20) . ' days')) : null;
            $paymentMethod = $status === 'paid' ? ['virement', 'carte', 'prélèvement'][rand(0, 2)] : null;
            
            $stmt->execute([
                $invoiceNumber, $organizerId, $eventId, $amount, $commission,
                $status, $dueDate, $paidDate, $paymentMethod, $createdAt
            ]);
        }
    }
    echo "  ✓ Created $invoiceCount invoices\n";
    
    // 8. CREATE ANALYTICS TABLE
    echo "\n9. Creating analytics table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS analytics_b2b (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Analytics table created\n";
    
    // Create mock analytics data
    echo "\n10. Creating mock analytics...\n";
    $stmt = $pdo->prepare("
        INSERT INTO analytics_b2b (
            organizer_id, metric_date, page_views, unique_visitors,
            conversion_rate, tickets_sold, revenue, avg_ticket_price
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($organizerIds as $organizerId) {
        // Create data for last 30 days
        for ($days = 30; $days >= 0; $days--) {
            $date = date('Y-m-d', strtotime("-$days days"));
            $pageViews = rand(100, 5000);
            $uniqueVisitors = intval(rand(50, intval($pageViews * 0.7)));
            $conversionRate = rand(1, 15);
            $ticketsSold = rand(0, 100);
            $avgPrice = rand(20, 80);
            $revenue = $ticketsSold * $avgPrice;
            
            $stmt->execute([
                $organizerId, $date, $pageViews, $uniqueVisitors,
                $conversionRate, $ticketsSold, $revenue, $avgPrice
            ]);
        }
    }
    echo "  ✓ Created analytics data for " . count($organizerIds) . " organizers\n";
    
    // 9. CREATE SUPPORT TICKETS TABLE
    echo "\n11. Creating support tickets table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS support_tickets (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Support tickets table created\n";
    
    // Create mock support tickets
    $ticketSubjects = [
        'Problème de paiement',
        'Question sur les commissions',
        'Demande de fonctionnalité',
        'Bug sur le dashboard',
        'Aide pour la création d\'événement',
        'Problème de billetterie',
        'Demande de remboursement',
        'Question sur l\'API'
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO support_tickets (
            organizer_id, subject, description, priority, status, category, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($organizerIds as $organizerId) {
        $numTickets = rand(0, 3);
        for ($i = 0; $i < $numTickets; $i++) {
            $subject = $ticketSubjects[array_rand($ticketSubjects)];
            $description = "J'ai un problème concernant $subject. Pouvez-vous m'aider?";
            $priority = ['low', 'medium', 'high', 'urgent'][rand(0, 3)];
            $status = ['open', 'in_progress', 'resolved', 'closed'][rand(0, 3)];
            $category = ['billing', 'technical', 'feature', 'other'][rand(0, 3)];
            $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 60) . ' days'));
            
            $stmt->execute([
                $organizerId, $subject, $description, $priority, $status, $category, $createdAt
            ]);
        }
    }
    echo "  ✓ Created support tickets\n";
    
    // Commit transaction
    $pdo->commit();
    
    echo "\n====================================\n";
    echo "✅ B2B Database seeding completed!\n";
    echo "====================================\n\n";
    
    echo "Summary:\n";
    echo "- " . count($organizerIds) . " organizers created\n";
    echo "- " . count($partners) . " partnerships created\n";
    echo "- $invoiceCount invoices generated\n";
    echo "- Analytics data for 30 days\n";
    echo "- Support tickets created\n";
    
    echo "\n📝 B2B Test Accounts:\n";
    echo "================================\n";
    echo "Organizer: contact@livenation.fr\n";
    echo "Organizer: culture@paris.fr\n";
    echo "Organizer: hello@indie-events.fr\n";
    echo "Password: organizer123\n";
    
    echo "\n💼 Subscription Plans:\n";
    echo "- Enterprise: BNP Paribas, Mairie de Paris\n";
    echo "- Pro: Jazz Association, Théâtre National\n";
    echo "- Starter: EventProd, Concerts & Co\n";
    echo "- Free: Indie Events\n";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Rolling back all changes...\n";
}

echo "</pre>";
?>