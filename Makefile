# Use bash for command execution for its advanced features like arrays and functions.
SHELL := /bin/bash

# --- Color Codes ---
COLOR_GREEN  := \e[1;32m
COLOR_PURPLE := \e[0;95m
COLOR_YELLOW := \e[0;33m
COLOR_RED    := \e[0;31m
COLOR_CYAN   := \e[0;36m
# --- Underline ---
UNDERLINE    := \e[4;37m
# --- Misc ---
COLOR_RESET  := \e[0m

# Default target to run when 'make' is called without arguments.
.DEFAULT_GOAL := help

master = english

help:
	@echo "════════════════════════════════════════════════════════════════════════════════"
	@echo "CORE COMMANDS"
	@echo "————————————————————————————————————————————————————————————————————————————————"
	@echo -e "$(COLOR_GREEN)make cs-check$(COLOR_RESET)   Coding standards check"
	@echo -e "$(COLOR_GREEN)make cs-fix$(COLOR_RESET)     Coding standards fix"
	@echo ""
	@echo -e "$(COLOR_GREEN)make module$(COLOR_RESET)     Creating installable OpenCart 4.x modules for languages."
	@echo -e "                $(COLOR_CYAN)Accepts the following parameter$(COLOR_RESET)"
	@echo -e "                $(COLOR_YELLOW)lng$(COLOR_RESET)      Create a module for the specified language"
	@echo -e "                         Optional, default $(UNDERLINE)english$(COLOR_RESET)"
	@echo -e "                         Calling without parameters will create modules"
	@echo -e "                         for all available languages."
	@echo -e "                $(COLOR_CYAN)Example$(COLOR_RESET)"
	@echo -e "                $(COLOR_PURPLE)make module$(COLOR_RESET)"
	@echo -e "             or $(COLOR_PURPLE)make module lng=french$(COLOR_RESET)"
	@echo ""
	@echo -e "$(COLOR_GREEN)make compare$(COLOR_RESET)    Compares two specified folders with language files."
	@echo -e "                $(COLOR_CYAN)Accepts the following parameters$(COLOR_RESET)"
	@echo -e "                $(COLOR_YELLOW)master$(COLOR_RESET)   Sample language for comparison."
	@echo -e "                         Optional, default $(UNDERLINE)english$(COLOR_RESET)"
	@echo -e "                $(COLOR_YELLOW)compare$(COLOR_RESET)  Language to compare with the sample."
	@echo -e "                $(COLOR_CYAN)Example$(COLOR_RESET)"
	@echo -e "                $(COLOR_PURPLE)make compare check=french$(COLOR_RESET)"
	@echo -e "             or $(COLOR_PURPLE)make compare master=french check=georgian$(COLOR_RESET)"
	@echo ""

module:
	@echo -e "$(COLOR_PURPLE)Creating installable modules for languages...$(COLOR_RESET)"
	@php .tools/module.php --lng $(lng)
	@echo -e "$(COLOR_GREEN)Done!$(COLOR_RESET)"

cs-check:
	@echo "Coding Standards Check"
	@php .tools/php-cs-fixer-v3.phar fix --config=.tools/php-cs-fixer.php --dry-run --diff

cs-fix:
	@echo "Coding Standards Fix"
	@php .tools/php-cs-fixer-v3.phar fix --config=.tools/php-cs-fixer.php

compare:
	@php .tools/compare.php --master $(master) --check $(check)
