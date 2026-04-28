DC_DEV = docker compose
DC_PROD = docker compose -f compose.prod.yaml

.PHONY: dev-up dev-start dev-stop dev-restart dev-down prod-up prod-start prod-stop prod-restart prod-down dev-serve frontend-shell backend-shell clean

dev-up:
	$(DC_DEV) up -d --build

dev-start:
	$(DC_DEV) up -d

dev-stop:
	$(DC_DEV) stop

dev-restart:
	$(DC_DEV) restart

dev-down:
	$(DC_DEV) down

prod-up:
	$(DC_PROD) up -d --build

prod-start:
	$(DC_PROD) up -d

prod-stop:
	$(DC_PROD) stop

prod-restart:
	$(DC_PROD) restart

prod-down:
	$(DC_PROD) down

dev-serve:
	$(DC_DEV) exec frontend npm run dev -- --host 0.0.0.0

frontend-shell:
	$(DC_DEV) exec frontend sh

backend-shell:
	$(DC_PROD) exec backend_php sh

clean:
	docker image prune
	docker compose down -v
	docker builder prune
