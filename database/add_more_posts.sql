-- Add more sample research posts

INSERT INTO research_posts (user_id, title, description) VALUES 
(1, 'Climate Change Data Analysis', 'Investigating global temperature trends and their correlation with carbon emissions using statistical models.'),
(1, 'Blockchain in Supply Chain', 'Research on implementing blockchain technology to improve transparency and traceability in supply chain management.'),
(1, 'Quantum Computing Applications', 'Exploring practical applications of quantum computing in cryptography and complex problem solving.'),
(1, 'Renewable Energy Optimization', 'Study on optimizing solar and wind energy systems for maximum efficiency and cost-effectiveness.'),
(1, 'Mental Health in Digital Age', 'Analyzing the impact of social media and digital technology on mental health among young adults.'),
(1, 'Cybersecurity Threat Detection', 'Developing AI-based systems for real-time detection and prevention of cyber attacks.'),
(1, 'Sustainable Agriculture Practices', 'Research on innovative farming techniques that reduce environmental impact while increasing crop yields.');

INSERT INTO comments (post_id, user_id, comment_text) VALUES 
(2, 1, 'Climate data analysis is crucial for understanding our environmental challenges.'),
(3, 1, 'Blockchain could revolutionize how we track products from source to consumer.'),
(4, 1, 'Quantum computing will change the future of technology as we know it.'),
(5, 1, 'Renewable energy is the key to a sustainable future for our planet.');