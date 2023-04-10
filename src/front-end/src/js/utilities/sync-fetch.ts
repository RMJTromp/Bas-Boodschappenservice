export interface HttpResponse {
    body: any,
    status: number,
    headers: {
        [key: string]: string
    },
    parser: DOMParser,
    json(): any,
    getAsDOM(): Document
}

let HttpResponse = function(xhr : XMLHttpRequest) {
    this.body = xhr.response;
    this.status = xhr.status;
    this.headers = xhr.getAllResponseHeaders().split("\r\n").reduce((result, current) => {
        let [name, value] = current.split(': ');
        result[name] = value;
        return result;
    })
    this.parser = new DOMParser();
}

HttpResponse.prototype.json = function() {
    return JSON.parse(this.body)
}

HttpResponse.prototype.getAsDOM = function() {
    return this.parser.parseFromString(this.body, "text/html")
}

export interface syncFetchRequestInit {
    method?: string,
    body?: string | Document | XMLHttpRequestBodyInit,
    options?: {
        checkProgress?: (event: ProgressEvent) => void
    },
    headers?: {
        [key: string]: string
    }
}

export default function syncFetch(url, init : syncFetchRequestInit = {}) : HttpResponse {
    const method = (init.method || "GET").toUpperCase()
    const headers = init.headers || {}
    const options = init.options || {}

    let xhr = new XMLHttpRequest()
    xhr.open(method, url, false)

    xhr.setRequestHeader("Content-Type", "*/*")
    for (const key in headers) {
        if (Object.hasOwnProperty.call(headers, key)) {
            xhr.setRequestHeader(key, headers[key])
        }
    }

    if (options && typeof options.checkProgress === 'function') {
        xhr.upload.onprogress = options.checkProgress
    }
    xhr.send(init.body)
    return new HttpResponse(xhr);
}
