-- Lieux supplementaires pour Mi-Findra
-- Provinces alignees sur la base existante (mi_findra.lieu)
-- Idempotent : re-executable sans creer de doublons

INSERT INTO lieu (designation, province)
SELECT t.designation, t.province
FROM (
    -- Analamanga
    SELECT 'Manjakandriana' AS designation, 'Analamanga' AS province
    UNION ALL SELECT 'Anjozorobe',      'Analamanga'
    UNION ALL SELECT 'Miantso',         'Analamanga'
    -- Haute Matsiatra
    UNION ALL SELECT 'Ambalavao',       'Haute Matsiatra'
    UNION ALL SELECT 'Ikalamavony',     'Haute Matsiatra'
    UNION ALL SELECT 'Isandra',         'Haute Matsiatra'
    -- Vatovavy
    UNION ALL SELECT 'Nosy Varika',     'Vatovavy'
    UNION ALL SELECT 'Ifanadiana',      'Vatovavy'
    UNION ALL SELECT 'Manakara',        'Vatovavy'
    -- Amoron''i Mania
    UNION ALL SELECT 'Fandriana',       'Amoron''i Mania'
    UNION ALL SELECT 'Ambatofinandrahana', 'Amoron''i Mania'
    -- Atsimo-Andrefana
    UNION ALL SELECT 'Sakaraha',        'Atsimo-Andrefana'
    UNION ALL SELECT 'Morombe',         'Atsimo-Andrefana'
    UNION ALL SELECT 'Betioky',         'Atsimo-Andrefana'
    -- Boeny
    UNION ALL SELECT 'Marovoay',        'Boeny'
    UNION ALL SELECT 'Ambato Boeni',    'Boeny'
    UNION ALL SELECT 'Sitampiky',       'Boeny'
    -- Alaotra Mangoro
    UNION ALL SELECT 'Ambatondrazaka',  'Alaotra Mangoro'
    UNION ALL SELECT 'Amparafaravola',  'Alaotra Mangoro'
    UNION ALL SELECT 'Andilamena',      'Alaotra Mangoro'
    -- Anosy
    UNION ALL SELECT 'Betroka',         'Anosy'
    UNION ALL SELECT 'Amboasary Sud',   'Anosy'
    -- Antsiranana
    UNION ALL SELECT 'Ambanja',         'Antsiranana'
    UNION ALL SELECT 'Vohemar',         'Antsiranana'
    UNION ALL SELECT 'Andapa',          'Antsiranana'
) t
WHERE NOT EXISTS (
    SELECT 1 FROM lieu l WHERE l.designation = t.designation
);
