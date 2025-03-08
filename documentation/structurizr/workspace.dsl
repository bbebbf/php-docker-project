workspace "Name" "Description" {

    !identifiers hierarchical

    model {
        user = person "User"
        softwaresystem_abc = softwareSystem "ABC Software System" {
            webapp = container "ABC Web Application"
            database = container "MS Sql Server" {
                tags "Database"
            }
            webapp -> database "Reads from and writes to"
        }

        user -> softwaresystem_abc "Uses"
    }

    views {
        systemContext softwaresystem_abc "Diagram1" {
            include *
            autolayout lr
        }

        container softwaresystem_abc "Diagram2" {
            include *
            autolayout lr
        }

        styles {
            element "Element" {
                color #ffffff
            }
            element "Person" {
                background #05527d
                shape person
            }
            element "Software System" {
                background #066296
            }
            element "Container" {
                background #0773af
            }
            element "Database" {
                background #ff7777
                shape cylinder
            }
        }
    }

    configuration {
        scope softwaresystem
    }

}