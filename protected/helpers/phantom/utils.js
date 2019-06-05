//UTILS
function toTitleCase(str)
{
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}

function serialize(obj) {
    var str = [],
            p;
    for (p in obj) {
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
    }
    return str.join("&");
}
;

function parseValue(v) {
    if (typeof v === 'undefined') {
        return true;
    } else {
        try {
            return JSON.parse(v);
        } catch (e) {
            return v;
        }
    }
}

function parseArguments(args) {
    return args.reduce(function (prev, current) {
        current = current.split('=');
        current[0] = current[0].replace(/^--/, '');
        prev[current[0]] = parseValue(current[1]);
        return prev;
    }, {});
}

function getParameterByName(url, name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(url);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function loadXMLDoc(filename) {
    if (window.XMLHttpRequest) {
        xhttp = new XMLHttpRequest();
    }
    xhttp.open("GET", filename, false);
    xhttp.send();
    return xhttp.responseXML;
}

function parseXml(xml, arrayTags)
{
    var dom = null;
    if (window.DOMParser)
    {
        dom = (new DOMParser()).parseFromString(xml, "text/xml");
    }
    else if (window.ActiveXObject)
    {
        dom = new ActiveXObject('Microsoft.XMLDOM');
        dom.async = false;
        if (!dom.loadXML(xml))
        {
            throw dom.parseError.reason + " " + dom.parseError.srcText;
        }
    }
    else
    {
        throw "cannot parse xml string!";
    }

    function isArray(o)
    {
        return Object.prototype.toString.apply(o) === '[object Array]';
    }

    function parseNode(xmlNode, result)
    {
        if (xmlNode.nodeName == "#text" && xmlNode.nodeValue.trim() == "")
        {
            return;
        }

        var jsonNode = {};
        var existing = result[xmlNode.nodeName];
        if (existing)
        {
            if (!isArray(existing))
            {
                result[xmlNode.nodeName] = [existing, jsonNode];
            }
            else
            {
                result[xmlNode.nodeName].push(jsonNode);
            }
        }
        else
        {
            if (arrayTags && arrayTags.indexOf(xmlNode.nodeName) != -1)
            {
                result[xmlNode.nodeName] = [jsonNode];
            }
            else
            {
                result[xmlNode.nodeName] = jsonNode;
            }
        }

        if (xmlNode.attributes)
        {
            var length = xmlNode.attributes.length;
            for (var i = 0; i < length; i++)
            {
                var attribute = xmlNode.attributes[i];
                jsonNode[attribute.nodeName] = attribute.nodeValue;
            }
        }

        var length = xmlNode.childNodes.length;
        for (var i = 0; i < length; i++)
        {
            parseNode(xmlNode.childNodes[i], jsonNode);
        }
    }

    var result = {};
    if (dom.childNodes.length)
    {
        parseNode(dom.childNodes[0], result);
    }

    return result;
}

function getUnitedStates() {
    return {
        'AL': 'ALABAMA',
        'AK': 'ALASKA',
        'AS': 'AMERICAN SAMOA',
        'AZ': 'ARIZONA',
        'AR': 'ARKANSAS',
        'CA': 'CALIFORNIA',
        'CO': 'COLORADO',
        'CT': 'CONNECTICUT',
        'DE': 'DELAWARE',
        'DC': 'DISTRICT OF COLUMBIA',
        'FM': 'FEDERATED STATES OF MICRONESIA',
        'FL': 'FLORIDA',
        'GA': 'GEORGIA',
        'GU': 'GUAM GU',
        'HI': 'HAWAII',
        'ID': 'IDAHO',
        'IL': 'ILLINOIS',
        'IN': 'INDIANA',
        'IA': 'IOWA',
        'KS': 'KANSAS',
        'KY': 'KENTUCKY',
        'LA': 'LOUISIANA',
        'ME': 'MAINE',
        'MH': 'MARSHALL ISLANDS',
        'MD': 'MARYLAND',
        'MA': 'MASSACHUSETTS',
        'MI': 'MICHIGAN',
        'MN': 'MINNESOTA',
        'MS': 'MISSISSIPPI',
        'MO': 'MISSOURI',
        'MT': 'MONTANA',
        'NE': 'NEBRASKA',
        'NV': 'NEVADA',
        'NH': 'NEW HAMPSHIRE',
        'NJ': 'NEW JERSEY',
        'NM': 'NEW MEXICO',
        'NY': 'NEW YORK',
        'NC': 'NORTH CAROLINA',
        'ND': 'NORTH DAKOTA',
        'MP': 'NORTHERN MARIANA ISLANDS',
        'OH': 'OHIO',
        'OK': 'OKLAHOMA',
        'OR': 'OREGON',
        'PW': 'PALAU',
        'PA': 'PENNSYLVANIA',
        'PR': 'PUERTO RICO',
        'RI': 'RHODE ISLAND',
        'SC': 'SOUTH CAROLINA',
        'SD': 'SOUTH DAKOTA',
        'TN': 'TENNESSEE',
        'TX': 'TEXAS',
        'UT': 'UTAH',
        'VT': 'VERMONT',
        'VI': 'VIRGIN ISLANDS',
        'VA': 'VIRGINIA',
        'WA': 'WASHINGTON',
        'WV': 'WEST VIRGINIA',
        'WI': 'WISCONSIN',
        'WY': 'WYOMING',
        'AE': 'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST',
        'AA': 'ARMED FORCES AMERICA (EXCEPT CANADA)',
        'AP': 'ARMED FORCES PACIFIC'
    };
}

String.prototype.ucfirst = function (notrim) {
    s = notrim ? this : this.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ');
    return s.length > 0 ? s.charAt(0).toUpperCase() + s.slice(1) : s;
};
//END UTILS