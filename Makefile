# Use bash for command execution for its advanced features like arrays and functions.
SHELL := /bin/bash

# --- Color Codes ---
# Using the 8-bit (256-color) palette for maximum compatibility.
COLOR_GREEN  := \e[1;32m
COLOR_PURPLE := \e[0;95m
COLOR_YELLOW := \e[0;33m
COLOR_RED    := \e[0;31m
COLOR_RESET  := \e[0m

# Default target to run when 'make' is called without arguments.
.DEFAULT_GOAL := help

master = english

help:
	@echo "════════════════════════════════════════════════════════════════════════════════"
	@echo "CORE COMMANDS"
	@echo "————————————————————————————————————————————————————————————————————————————————"
	@echo -e "$(COLOR_GREEN)make cs-check$(COLOR_RESET)       Coding standards check"
	@echo -e "$(COLOR_GREEN)make cs-fix$(COLOR_RESET)         Coding standards fix"
	@echo ""
	@echo -e "$(COLOR_GREEN)make module$(COLOR_RESET)         Creating installable OpenCart 4.x modules for languages."
	@echo -e "$(COLOR_GREEN)make compare-files$(COLOR_RESET)  Compares two specified folders with language files."
	@echo -e "                    Accepts the following parameters:"
	@echo -e "           $(COLOR_YELLOW)master$(COLOR_RESET)   Sample language for comparison $(COLOR_PURPLE)[optional, default \"english\"]$(COLOR_RESET)"
	@echo -e "          $(COLOR_YELLOW)compare$(COLOR_RESET)   Language to compare with the sample."
	@echo -e "                    Example:"
	@echo -e "                       $(COLOR_GREEN)make compare-files check=french$(COLOR_RESET)"
	@echo -e "                    or $(COLOR_GREEN)make compare-files master=english check=french$(COLOR_RESET)"
	@echo ""

module:
	@echo -e "$(COLOR_PURPLE)Creating installable modules for languages...$(COLOR_RESET)"
	@php .tools/module.php
	@echo -e "$(COLOR_GREEN)Done!$(COLOR_RESET)"

cs-check:
	@echo "Coding Standards Check"
	@php .tools/php-cs-fixer-v3.phar fix --config=.tools/php-cs-fixer.php --dry-run --diff

cs-fix:
	@echo "Coding Standards Fix"
	@php .tools/php-cs-fixer-v3.phar fix --config=.tools/php-cs-fixer.php

compare-files:
	@php .tools/compare-files.php --master $(master) --check $(check)
