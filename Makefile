BUILDDIR = build/
WEBAPP = src/webapp/
SASS=src/scss/
SCHEMAPATH=${BUILDDIR}beeo/dbschema.php
DIR="/etc/webapps/beeo"

# colours
CGREEN   = "\\e[32m"
CYELLOW  = "\\e[33m"
CCYAN    = "\\e[36m"
CLCYAN   = "\\e[96m"
CBOLD    = "\\e[1m"
CNONE    = "\\e[0m"
CVOID    = "\\033[2K\\r"

_build: clean
	@printf "${CLCYAN}${CBOLD}Building...${CNONE}"
	@ #rm -rf ${BUILDDIR}
	@mkdir -p ${BUILDDIR}
	@cp -r ${WEBAPP}* ${BUILDDIR}
	@ # build the css'
	@mkdir -p "${BUILDDIR}css"
	@sass --style compressed ${SASS}main.scss:${BUILDDIR}css/main.css
	@sass --style compressed ${SASS}themes/themelightpink.scss:${BUILDDIR}css/themelightpink.css
	@sass --style compressed ${SASS}themes/themedarkpink.scss:${BUILDDIR}css/themedarkpink.css
	@sass --style compressed ${SASS}themes/themelightcyan.scss:${BUILDDIR}css/themelightcyan.css
	@sass --style compressed ${SASS}themes/themedarknap.scss:${BUILDDIR}css/themedarknap.css
	@rm -rf ${BUILDDIR}css/*.map
	@rm -rf .sass-cache
	@ # build db schema
	@echo '<?php return <<<EOT' > ${SCHEMAPATH}
	@cat "src/psqlschema/00-base.sql" >> ${SCHEMAPATH}
	@echo 'EOT;' >> ${SCHEMAPATH}
	@echo '?>' >> ${SCHEMAPATH}
	@ # fix permissions
	@chmod -R go+r ${BUILDDIR}*
	@printf "${CVOID}${CGREEN}Built!${CNONE}\n"

clean:
	@printf "${CLCYAN}${CBOLD}Cleaning...${CNONE}"
	@rm -rf ${BUILDDIR}
	@printf "${CVOID}${CLCYAN}Cleaned${CNONE}\n"

install: _build
	@printf "${CLCYAN}${CBOLD}Installing at ${DIR}...${CNONE}"
	@rm -rf ${DIR}
	@mkdir ${DIR}
	@cp -r ${BUILDDIR}* ${DIR}
	@printf "${CVOID}${CGREEN}Installed at ${DIR}!${CNONE}\n"

update: _build
	@printf "${CLCYAN}${CBOLD}Updating ${DIR}...${CNONE}"
	@cp "${DIR}/config.php" "${BUILDDIR}config.php"
	@rm -rf ${DIR}
	@mkdir ${DIR}
	@cp -r ${BUILDDIR}* ${DIR}
	@printf "${CVOID}${CGREEN}Updated ${DIR}!${CNONE}\n"

teste:
	@echo ${DIR}
