# Comando interno para optimizar el autoloader antes de las tareas (Oculto en el help)
_dump:
    @composer dump-autoload --optimize

# Ejecuta todo el pipeline de análisis y tests en todo el proyecto
check-all: _dump
    tools/grumphp run

# Simula el filtro estricto que se ejecutará en el pre-commit de Git
check-commit: _dump
    tools/grumphp git:pre-commit

# Simula el filtro estricto que se ejecutará al hacer un git push (Tests + Infection)
check-push: _dump
    tools/grumphp run --testsuite=git_pre_push

# Ejecuta Rector en modo análisis sin alterar archivos
rector-dry: _dump
    tools/grumphp run --tasks=rector

# Ejecuta Rector aplicando los cambios automáticos en el código
rector-fix params="": _dump
    bin/rector process {{params}}

# Ejecuta todos los tests unitarios sin cobertura (Rápido)
tests-run params="tests": _dump
    bin/phpunit --no-coverage {{params}}

# Ejecuta todos los tests unitarios sin cobertura (Rápido)
tests-unit: _dump
    bin/phpunit --no-coverage --testsuite Unit

# Ejecuta todos los tests de integración sin cobertura (Rápido)
tests-integration: _dump
    bin/phpunit --no-coverage --testsuite Integration


# Ejecuta los tests activando la cobertura HTML (Requires Xdebug/PCOV)
tests-coverage params="tests": _dump
    XDEBUG_MODE=coverage bin/phpunit --coverage-text --coverage-clover=var/coverage/clover.xml --coverage-html=var/coverage {{params}}

# Ejecuta el análisis de mutaciones con Infection (Requiere tests en verde)
tests-mutation flags="": _dump
    XDEBUG_MODE=coverage tools/infection --configuration=infection.json {{flags}}

# Ejecuta PHP-CS-Fixer para corregir el estilo del código automáticamente
cs-fix: _dump
    tools/php-cs-fixer fix

# Ejecuta PHPStan analizando todo el proyecto
phpstan: _dump
    tools/phpstan analyse src --level=max --no-progress --error-format=json --memory-limit=512M | jq

# Aplica fixups automáticos analizando el último commit de cada archivo en el staging area
git-auto-fixup: _dump
    git auto-fixup

# Prepara un rebase interactivo desde --root usando la configuración avanzada de tu gitconfig
git-rebase-interactive:
    git rebase-interactive

# Crea un nuevo Tag semántico, genera el Changelog y sube los cambios a Git (Usa alias inteligente)
git-release:
    git release

# Simula la creación del Tag y muestra el Changelog que generaría
git-release-dry:
    git release --dry-run

# Abre el selector interactivo para eliminar un tag en local y remoto
git-tag-kill:
    git tag-kill

# Limpia por completo todas las cachés, reportes y logs generados
clean:
    rm -rf var/cache/* var/coverage/* var/log/* var/report/*