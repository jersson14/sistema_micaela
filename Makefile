# Makefile para facilitar comandos Docker
# Uso: make [comando]

.PHONY: help build up down restart logs logs-app logs-db shell shell-db backup restore clean

help: ## Mostrar esta ayuda
	@echo "Comandos disponibles:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

build: ## Construir las imágenes Docker
	docker-compose build

up: ## Levantar los servicios
	docker-compose up -d
	@echo "✅ Servicios iniciados"
	@echo "📍 Aplicación: http://localhost:8080"
	@echo "📊 phpMyAdmin: http://localhost:8081"

down: ## Detener los servicios
	docker-compose down

restart: ## Reiniciar los servicios
	docker-compose restart

logs: ## Ver logs de todos los servicios
	docker-compose logs -f

logs-app: ## Ver logs de la aplicación
	docker-compose logs -f app

logs-db: ## Ver logs de MySQL
	docker-compose logs -f db

shell: ## Acceder al contenedor de la aplicación
	docker exec -it tours_micaela_app bash

shell-db: ## Acceder a MySQL
	docker exec -it tours_micaela_db mysql -uroot -proot_password_2024 micaela

backup: ## Hacer backup de la base de datos
	@mkdir -p backup
	docker exec tours_micaela_db mysqldump -uroot -proot_password_2024 micaela > backup/micaela_$$(date +%Y%m%d_%H%M%S).sql
	@echo "✅ Backup creado en backup/"

restore: ## Restaurar backup (usar: make restore FILE=backup/archivo.sql)
	@if [ -z "$(FILE)" ]; then \
		echo "❌ Error: Especifica el archivo con FILE=backup/archivo.sql"; \
		exit 1; \
	fi
	docker exec -i tours_micaela_db mysql -uroot -proot_password_2024 micaela < $(FILE)
	@echo "✅ Backup restaurado"

clean: ## Limpiar contenedores y volúmenes (⚠️ CUIDADO: Borra la BD)
	docker-compose down -v
	@echo "⚠️  Contenedores y volúmenes eliminados"

status: ## Ver estado de los contenedores
	docker-compose ps

install: build up ## Instalación completa (build + up)
	@echo "✅ Instalación completada"
	@echo "📍 Accede a: http://localhost:8080"
