DC_DEV = docker compose
DC_PROD = docker compose -f compose.prod.yaml

.PHONY: dev-up dev-down prod-up prod-down serve shell clean

dev-up:
	$(DC_DEV) up -d --build

dev-down:
	$(DC_DEV) down

prod-up:
	$(DC_PROD) up -d --build

prod-down:
	$(DC_PROD) down

serve:
	$(DC_DEV) exec frontend npm run dev -- --host 0.0.0.0

shell:
	$(DC_DEV) exec frontend sh

clean:
	docker image prune
	docker compose down -v
	docker builder prune
