# Use bash for command execution for its advanced features like arrays and functions.
SHELL := /bin/bash

# --- Color Codes ---
# Using the 8-bit (256-color) palette for maximum compatibility.
COLOR_GREEN := \e[1;32m
COLOR_PURPLE := \e[0;95m
COLOR_RED := \e[0;31m
COLOR_RESET := \e[0m

# Default target to run when 'make' is called without arguments.
.DEFAULT_GOAL := help

help: ## Show this help message
	@echo "════════════════════════════════════════════════════════════"
	@echo "CORE COMMANDS"
	@echo "————————————————————————————————————————————————————————————"
	@echo -e "$(COLOR_GREEN)make module$(COLOR_RESET)    $(COLOR_PURPLE)Creating installable modules for languages$(COLOR_RESET)"
	@echo -e "$(COLOR_GREEN)make cs$(COLOR_RESET)        $(COLOR_PURPLE)Coding standards check$(COLOR_RESET)"
	@echo -e "$(COLOR_GREEN)make cs-fix$(COLOR_RESET)    $(COLOR_PURPLE)Coding standards fix$(COLOR_RESET)"
	@echo ""

module:
	@echo -e "$(COLOR_PURPLE)Creating installable modules for languages...$(COLOR_RESET)"
	@php bin/module
	@echo -e "$(COLOR_GREEN)Done!$(COLOR_RESET)"

cs:
	@echo "Coding Standards Check"
	php bin/php-cs-fixer-v3.phar fix --config=bin/php-cs-fixer.php --dry-run --diff

cs-fix:
	@echo "Coding Standards Fix"
	php bin/php-cs-fixer-v3.phar fix --config=bin/php-cs-fixer.php
