
# Leer los archivos PHP y HTML para analizar su estructura
with open('indexCasa.php', 'r', encoding='utf-8') as f:
    index_casa = f.read()

with open('admin.html', 'r', encoding='utf-8') as f:
    admin_html = f.read()

with open('styles.css', 'r', encoding='utf-8') as f:
    styles_css = f.read()

# Mostrar primeros caracteres para análisis
print("=== indexCasa.php (primeros 2000 caracteres) ===")
print(index_casa[:2000])
print("\n\n=== admin.html (completo) ===")
print(admin_html)
print("\n\n=== styles.css (completo) ===")
print(styles_css)
