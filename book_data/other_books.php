<?php
// Other Categories - 3000 Books distributed across all genres

$otherBooks = [];

// Science Fiction (Genre 4) - ~200 books
$scifiAuthors = ['Isaac Asimov', 'Arthur C. Clarke', 'Philip K. Dick', 'Frank Herbert', 'Ray Bradbury', 'Ursula K. Le Guin', 'Robert A. Heinlein', 'H.G. Wells', 'Jules Verne'];
$scifiTitles = ['Foundation', 'Dune', 'Neuromancer', 'Ender\'s Game', 'The Left Hand of Darkness', 'Stranger in a Strange Land', 'The Time Machine', '2001: A Space Odyssey', 'Do Androids Dream of Electric Sheep?', 'Fahrenheit 451', 'The War of the Worlds', 'Twenty Thousand Leagues Under the Sea'];

for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => ($i < count($scifiTitles)) ? $scifiTitles[$i] : 'Sci-Fi Novel ' . ($i + 1),
        'author' => $scifiAuthors[array_rand($scifiAuthors)],
        'year' => rand(1950, 2024),
        'pages' => rand(200, 600),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Bilim kurgu edebiyatının önemli eserlerinden.',
        'genre' => 4
    ];
}

// Fantasy (Genre 5) - ~200 books
$fantasyAuthors = ['J.R.R. Tolkien', 'George R.R. Martin', 'J.K. Rowling', 'Terry Pratchett', 'Brandon Sanderson', 'Patrick Rothfuss', 'Neil Gaiman', 'C.S. Lewis'];
$fantasyTitles = ['The Lord of the Rings', 'The Hobbit', 'Harry Potter', 'A Game of Thrones', 'The Name of the Wind', 'American Gods', 'The Chronicles of Narnia', 'Good Omens'];

for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => ($i < count($fantasyTitles)) ? $fantasyTitles[$i] : 'Fantasy Epic ' . ($i + 1),
        'author' => $fantasyAuthors[array_rand($fantasyAuthors)],
        'year' => rand(1950, 2024),
        'pages' => rand(300, 1000),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Fantastik edebiyatın büyüleyici dünyası.',
        'genre' => 5
    ];
}

// Mystery/Thriller (Genre 8) - ~200 books
$mysteryAuthors = ['Agatha Christie', 'Arthur Conan Doyle', 'Dan Brown', 'Gillian Flynn', 'Stieg Larsson', 'Raymond Chandler', 'Dashiell Hammett', 'Patricia Highsmith'];
$mysteryTitles = ['Murder on the Orient Express', 'The Da Vinci Code', 'Gone Girl', 'The Girl with the Dragon Tattoo', 'The Maltese Falcon', 'The Big Sleep', 'The Hound of the Baskervilles'];

for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => ($i < count($mysteryTitles)) ? $mysteryTitles[$i] : 'Mystery Novel ' . ($i + 1),
        'author' => $mysteryAuthors[array_rand($mysteryAuthors)],
        'year' => rand(1920, 2024),
        'pages' => rand(250, 500),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Gerilim ve gizem dolu polisiye roman.',
        'genre' => 8
    ];
}

// Romance (Genre 6) - ~150 books
$romanceAuthors = ['Nicholas Sparks', 'Nora Roberts', 'Danielle Steel', 'Jojo Moyes', 'Colleen Hoover', 'Emily Brontë', 'Jane Austen'];
for ($i = 0; $i < 150; $i++) {
    $otherBooks[] = [
        'title' => 'Love Story ' . ($i + 1),
        'author' => $romanceAuthors[array_rand($romanceAuthors)],
        'year' => rand(1950, 2024),
        'pages' => rand(200, 400),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Duygusal ve romantik bir aşk hikayesi.',
        'genre' => 6
    ];
}

// Horror (Genre 11) - ~150 books
$horrorAuthors = ['Stephen King', 'H.P. Lovecraft', 'Edgar Allan Poe', 'Bram Stoker', 'Mary Shelley', 'Shirley Jackson', 'Clive Barker'];
for ($i = 0; $i < 150; $i++) {
    $otherBooks[] = [
        'title' => 'Horror Tale ' . ($i + 1),
        'author' => $horrorAuthors[array_rand($horrorAuthors)],
        'year' => rand(1900, 2024),
        'pages' => rand(200, 600),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Korku ve gerilim dolu ürkütücü hikaye.',
        'genre' => 11
    ];
}

// Biography (Genre 9) - ~200 books
$biographySubjects = ['Steve Jobs', 'Elon Musk', 'Leonardo da Vinci', 'Albert Einstein', 'Marie Curie', 'Nelson Mandela', 'Mahatma Gandhi', 'Winston Churchill'];
for ($i = 0; $i < 200; $i++) {
    $subject = $biographySubjects[array_rand($biographySubjects)];
    $otherBooks[] = [
        'title' => 'Biography of ' . $subject,
        'author' => 'Walter Isaacson',
        'year' => rand(1990, 2024),
        'pages' => rand(400, 800),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => $subject . ' hakkında kapsamlı biyografi.',
        'genre' => 9
    ];
}

// History (Genre 3) - ~200 books
$historyTopics = ['World War II', 'Ancient Rome', 'Ottoman Empire', 'Renaissance', 'Industrial Revolution', 'Cold War', 'Ancient Egypt', 'Medieval Europe'];
for ($i = 0; $i < 200; $i++) {
    $topic = $historyTopics[array_rand($historyTopics)];
    $otherBooks[] = [
        'title' => 'History of ' . $topic,
        'author' => 'İlber Ortaylı',
        'year' => rand(1980, 2024),
        'pages' => rand(300, 700),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => $topic . ' dönemi hakkında detaylı tarih çalışması.',
        'genre' => 3
    ];
}

// Science (Genre 13) - ~200 books
$scienceAuthors = ['Stephen Hawking', 'Carl Sagan', 'Richard Dawkins', 'Neil deGrasse Tyson', 'Brian Greene', 'Michio Kaku'];
$scienceTopics = ['Cosmos', 'Brief History of Time', 'The Selfish Gene', 'Astrophysics', 'Quantum Physics', 'Evolution', 'The Universe'];
for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => $scienceTopics[array_rand($scienceTopics)] . ' ' . ($i + 1),
        'author' => $scienceAuthors[array_rand($scienceAuthors)],
        'year' => rand(1970, 2024),
        'pages' => rand(250, 500),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Bilim ve evren hakkında ilham verici çalışma.',
        'genre' => 13
    ];
}

// Psychology (Genre 10) - ~200 books
$psychAuthors = ['Sigmund Freud', 'Carl Jung', 'Viktor Frankl', 'Daniel Kahneman', 'Malcolm Gladwell', 'Erich Fromm'];
for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => 'Psychology Study ' . ($i + 1),
        'author' => $psychAuthors[array_rand($psychAuthors)],
        'year' => rand(1950, 2024),
        'pages' => rand(200, 500),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'İnsan psikolojisi ve davranışları üzerine.',
        'genre' => 10
    ];
}

// Philosophy (Genre 8) - ~200 books
$philoAuthors = ['Friedrich Nietzsche', 'Plato', 'Aristotle', 'Immanuel Kant', 'Jean-Paul Sartre', 'Søren Kierkegaard', 'Arthur Schopenhauer'];
for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => 'Philosophy Work ' . ($i + 1),
        'author' => $philoAuthors[array_rand($philoAuthors)],
        'year' => rand(1800, 2024),
        'pages' => rand(200, 600),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Felsefe ve düşünce tarihi üzerine önemli eser.',
        'genre' => 8
    ];
}

// Self-Help (Genre 7) - ~200 books
$selfHelpAuthors = ['Dale Carnegie', 'Stephen Covey', 'Tony Robbins', 'James Clear', 'Cal Newport', 'Charles Duhigg', 'Robert Kiyosaki'];
for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => 'Self Improvement ' . ($i + 1),
        'author' => $selfHelpAuthors[array_rand($selfHelpAuthors)],
        'year' => rand(1980, 2024),
        'pages' => rand(200, 400),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Kişisel gelişim ve başarı rehberi.',
        'genre' => 7
    ];
}

// Poetry (Genre 2) - ~200 books
$poetAuthors = ['Pablo Neruda', 'Rumi', 'Hafiz', 'Walt Whitman', 'Emily Dickinson', 'T.S. Eliot', 'Sylvia Plath'];
for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => 'Poetry Collection ' . ($i + 1),
        'author' => $poetAuthors[array_rand($poetAuthors)],
        'year' => rand(1900, 2024),
        'pages' => rand(100, 300),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Şiir ve manzum eserler koleksiyonu.',
        'genre' => 2
    ];
}

// Art & Culture (Genre 12) - ~200 books
for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => 'Art & Culture ' . ($i + 1),
        'author' => 'Various Artists',
        'year' => rand(1950, 2024),
        'pages' => rand(200, 400),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Sanat ve kültür üzerine kapsamlı çalışma.',
        'genre' => 12
    ];
}

// Adventure (Genre 12) - ~200 books
$adventureAuthors = ['Jack London', 'Jules Verne', 'Daniel Defoe', 'Herman Melville', 'Robert Louis Stevenson'];
for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => 'Adventure Story ' . ($i + 1),
        'author' => $adventureAuthors[array_rand($adventureAuthors)],
        'year' => rand(1850, 2024),
        'pages' => rand(250, 500),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Macera dolu heyecan verici hikaye.',
        'genre' => 12
    ];
}

// Business & Economics (Genre 7) - ~200 books
for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => 'Business & Economics ' . ($i + 1),
        'author' => 'Business Expert',
        'year' => rand(1990, 2024),
        'pages' => rand(200, 400),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'İş dünyası ve ekonomi üzerine rehber.',
        'genre' => 7
    ];
}

// Technology (Genre 13) - ~200 books
for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => 'Technology Guide ' . ($i + 1),
        'author' => 'Tech Expert',
        'year' => rand(2000, 2024),
        'pages' => rand(200, 500),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Teknoloji ve dijital dünya rehberi.',
        'genre' => 13
    ];
}

// Children's Books (Genre 5) - ~200 books
for ($i = 0; $i < 200; $i++) {
    $otherBooks[] = [
        'title' => 'Children\'s Story ' . ($i + 1),
        'author' => 'Children\'s Author',
        'year' => rand(1950, 2024),
        'pages' => rand(50, 200),
        'cover' => "https://covers.openlibrary.org/b/isbn/978" . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT) . "-M.jpg",
        'desc' => 'Çocuklar için eğitici ve eğlenceli hikaye.',
        'genre' => 5
    ];
}

?>