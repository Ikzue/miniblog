export function addTableData(tableRow, apiResponse, attr, href=""){
    /** 
     * Construct a td cell to a table row from an API response object.
     * @param {HTMLElement} tableRow - Row where td is added
     * @param {Object} apiResponse   - API response
     * @param {string} attr          - Attribute patch. (e.g. "user.display" to get a post's user)
     * @param {string} href          - Optional URL if we want an anchor
    */
    
    const td = document.createElement('td');
    let textContent = attr.split('.').reduce(
        (acc, key)=> {
            if (acc === undefined || acc === null) {
                return null;
            }
            return acc&&acc[key];
        },
        apiResponse
    );
    if (href) {
        const a = document.createElement("a");
        a.className = "clickable";
        a.href = href;
        a.textContent = textContent;
        td.appendChild(a);
    }
    else{
        td.textContent = textContent;
    }

    tableRow.appendChild(td);
}