package main

import (
    "bytes"
    "encoding/json"
    "fmt"
    "net/http"
    "strings"

    "github.com/headzoo/surf"
    "github.com/headzoo/surf/agent"
    "github.com/PuerkitoBio/goquery"
)

type Game struct {
    Name           string   `json:"name"`
    Background     string   `json:"background"`
    Cover          string   `json:"cover"`
    Value          string   `json:"value"`
    Value_discount string   `json:"value_discount"`
    Studio         string   `json:"studio"`
    Description    string   `json:"description"`
    Discount       bool     `json:"discount"`
    Type           string   `json:"type"`
    Size           string   `json:"size"`
    Addons         []string `json:"addons"`
}

func crawl(w http.ResponseWriter, r *http.Request) {
    link := r.URL.Query().Get("link")
    if link == "" {
        return
    }

    bow := surf.NewBrowser()
    bow.SetUserAgent(agent.Chrome())
    err := bow.Open(link)
    if err != nil {
        panic(err)
    }

    title := bow.Find(".c-heading-2.srv_title")
    titleres := title.Text()

    background := bow.Find(".context-image-cover ")
    attr, _ := background.Attr("style")
    format := strings.Replace(attr, "background-image: url(", "", -1)
    format = strings.Replace(format, ");", "", -1)
    backgroundres := format

    cover := bow.Find(".srv_appHeaderBoxArt img")
    covattr, _ := cover.Attr("src")
    coverres := covattr

    prix := bow.Find(".c-price .srv_microdata meta")
    lol, _ := prix.Attr("content")
    prixres := lol

    s := bow.Find(".price-disclaimer span")

    wtype := bow.Find(".body meta")
    typeF, _ := wtype.Attr("content")
    typeres := typeF

    var wsize string

     bow.Find(".c-content-toggle span").Each(func(i int, s *goquery.Selection) {
         if i == 4 {
             wsize = s.Text()
         }
     })

    var linkt []string

    bow.Find("#addonswithdetails .slide").Each(func(i int, s *goquery.Selection) {
            links, _ := s.Find("a").First().Attr("href")
            linkt= append(linkt, links)

    })

    var overpriceres string

    bow.Find(".srv_microdata").Each(func(i int, s *goquery.Selection) {
        if i == 3 {
            ovrprice, _ := s.Find("meta").First().Attr("content")
            overpriceres = ovrprice
        }
    })

    overprice := s.First().Text()

    if overprice == "" {
        overprice = "0"
    }

    var resultdiscount bool
    if overpriceres != "" {
        if overpriceres == "0" {
            resultdiscount = false
        } else {
            resultdiscount = true
        }
    } else {
        resultdiscount = false
    }

    if overprice == "0" {
        resultdiscount = false
    } else {
        resultdiscount = true
    }

    studio := bow.Find(".c-content-toggle span span").First().Text()
    thestudio := strings.Replace(studio, " ", "", -1)
    thestudio = strings.Replace(studio, "\n", "", -1)

    desc := bow.Find(".c-content-toggle p").First().Text()


    result := Game{
        Name:           titleres,
        Background:     backgroundres,
        Cover:          coverres,
        Value:          prixres,
        Value_discount: overpriceres,
        Studio:         thestudio,
        Description:    desc,
        Discount:       resultdiscount,
        Addons:         linkt,
        Type:           typeres,
        Size:           wsize,
    }

    b, err := json.Marshal(result)
    b = bytes.Replace(b, []byte("\\u003c"), []byte("<"), -1)
    b = bytes.Replace(b, []byte("\\u003e"), []byte(">"), -1)
    b = bytes.Replace(b, []byte("\\u0026"), []byte("&"), -1)
    if err != nil {
        fmt.Println(err)
    }


    w.Write([]byte(b))
}

func main() {
    fmt.Println("CRAWLER START (add background,studio,description,discount)")
    http.HandleFunc("/", crawl)
    http.ListenAndServe(":10123", nil)
}
