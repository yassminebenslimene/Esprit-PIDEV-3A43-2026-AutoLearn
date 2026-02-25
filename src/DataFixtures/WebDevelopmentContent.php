<?php

namespace App\DataFixtures;

trait WebDevelopmentContent
{
    private function getChapter1Content(): string
    {
        return '<h2>Welcome to Web Development</h2>
<p>HTML (HyperText Markup Language) is the foundation of all websites. It provides the structure and content of web pages.</p>

<h3>What is HTML?</h3>
<ul>
    <li><strong>Markup Language</strong>: Uses tags to define elements</li>
    <li><strong>Structure</strong>: Organizes content hierarchically</li>
    <li><strong>Semantic</strong>: Gives meaning to content</li>
    <li><strong>Universal</strong>: Works on all browsers and devices</li>
</ul>

<h3>Basic HTML Document Structure</h3>
<pre><code>&lt;!DOCTYPE html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
    &lt;title&gt;My First Web Page&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Hello, World!&lt;/h1&gt;
    &lt;p&gt;Welcome to web development!&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>

<h3>Essential HTML Tags</h3>
<ul>
    <li><code>&lt;h1&gt; to &lt;h6&gt;</code>: Headings</li>
    <li><code>&lt;p&gt;</code>: Paragraphs</li>
    <li><code>&lt;a&gt;</code>: Links</li>
    <li><code>&lt;img&gt;</code>: Images</li>
    <li><code>&lt;div&gt;</code>: Container</li>
    <li><code>&lt;span&gt;</code>: Inline container</li>
</ul>

<h3>Text Formatting</h3>
<pre><code>&lt;p&gt;This is &lt;strong&gt;bold&lt;/strong&gt; text.&lt;/p&gt;
&lt;p&gt;This is &lt;em&gt;italic&lt;/em&gt; text.&lt;/p&gt;
&lt;p&gt;This is &lt;mark&gt;highlighted&lt;/mark&gt; text.&lt;/p&gt;
&lt;p&gt;This is &lt;del&gt;deleted&lt;/del&gt; text.&lt;/p&gt;</code></pre>

<h3>Links and Images</h3>
<pre><code>&lt;!-- Link --&gt;
&lt;a href="https://example.com"&gt;Visit Example&lt;/a&gt;

&lt;!-- Image --&gt;
&lt;img src="image.jpg" alt="Description" width="300"&gt;</code></pre>';
    }

    private function getChapter2Content(): string
    {
        return '<h2>HTML Structure and Semantics</h2>
<p>Semantic HTML uses tags that clearly describe their meaning to both the browser and the developer.</p>

<h3>Semantic Elements</h3>
<pre><code>&lt;header&gt;
    &lt;nav&gt;
        &lt;ul&gt;
            &lt;li&gt;&lt;a href="#home"&gt;Home&lt;/a&gt;&lt;/li&gt;
            &lt;li&gt;&lt;a href="#about"&gt;About&lt;/a&gt;&lt;/li&gt;
        &lt;/ul&gt;
    &lt;/nav&gt;
&lt;/header&gt;

&lt;main&gt;
    &lt;article&gt;
        &lt;h2&gt;Article Title&lt;/h2&gt;
        &lt;p&gt;Article content...&lt;/p&gt;
    &lt;/article&gt;
    
    &lt;aside&gt;
        &lt;h3&gt;Related Links&lt;/h3&gt;
    &lt;/aside&gt;
&lt;/main&gt;

&lt;footer&gt;
    &lt;p&gt;&copy; 2024 My Website&lt;/p&gt;
&lt;/footer&gt;</code></pre>

<h3>Lists</h3>
<pre><code>&lt;!-- Unordered List --&gt;
&lt;ul&gt;
    &lt;li&gt;Item 1&lt;/li&gt;
    &lt;li&gt;Item 2&lt;/li&gt;
    &lt;li&gt;Item 3&lt;/li&gt;
&lt;/ul&gt;

&lt;!-- Ordered List --&gt;
&lt;ol&gt;
    &lt;li&gt;First&lt;/li&gt;
    &lt;li&gt;Second&lt;/li&gt;
    &lt;li&gt;Third&lt;/li&gt;
&lt;/ol&gt;</code></pre>

<h3>Tables</h3>
<pre><code>&lt;table&gt;
    &lt;thead&gt;
        &lt;tr&gt;
            &lt;th&gt;Name&lt;/th&gt;
            &lt;th&gt;Age&lt;/th&gt;
        &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
        &lt;tr&gt;
            &lt;td&gt;Alice&lt;/td&gt;
            &lt;td&gt;25&lt;/td&gt;
        &lt;/tr&gt;
    &lt;/tbody&gt;
&lt;/table&gt;</code></pre>

<h3>Forms</h3>
<pre><code>&lt;form action="/submit" method="POST"&gt;
    &lt;label for="name"&gt;Name:&lt;/label&gt;
    &lt;input type="text" id="name" name="name" required&gt;
    
    &lt;label for="email"&gt;Email:&lt;/label&gt;
    &lt;input type="email" id="email" name="email" required&gt;
    
    &lt;button type="submit"&gt;Submit&lt;/button&gt;
&lt;/form&gt;</code></pre>';
    }

    private function getChapter3Content(): string
    {
        return '<h2>Introduction to CSS</h2>
<p>CSS (Cascading Style Sheets) is used to style and layout web pages.</p>

<h3>CSS Syntax</h3>
<pre><code>selector {
    property: value;
}</code></pre>

<h3>Ways to Add CSS</h3>
<pre><code>&lt;!-- Inline CSS --&gt;
&lt;p style="color: blue;"&gt;Blue text&lt;/p&gt;

&lt;!-- Internal CSS --&gt;
&lt;style&gt;
    p {
        color: blue;
    }
&lt;/style&gt;

&lt;!-- External CSS --&gt;
&lt;link rel="stylesheet" href="styles.css"&gt;</code></pre>

<h3>Selectors</h3>
<pre><code>/* Element selector */
p {
    color: black;
}

/* Class selector */
.highlight {
    background-color: yellow;
}

/* ID selector */
#header {
    font-size: 24px;
}

/* Descendant selector */
div p {
    margin: 10px;
}</code></pre>

<h3>Colors and Backgrounds</h3>
<pre><code>.box {
    color: #333;
    background-color: #f0f0f0;
    background-image: url("bg.jpg");
    background-size: cover;
}</code></pre>

<h3>Text Styling</h3>
<pre><code>.text {
    font-family: Arial, sans-serif;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    text-decoration: underline;
    line-height: 1.5;
}</code></pre>

<h3>Box Model</h3>
<pre><code>.box {
    width: 300px;
    height: 200px;
    padding: 20px;
    border: 2px solid black;
    margin: 10px;
}</code></pre>';
    }

    private function getChapter4Content(): string
    {
        return '<h2>CSS Layout and Positioning</h2>
<p>Learn how to position and layout elements on your web page.</p>

<h3>Display Property</h3>
<pre><code>.block {
    display: block;  /* Takes full width */
}

.inline {
    display: inline;  /* Flows with text */
}

.inline-block {
    display: inline-block;  /* Inline but with width/height */
}

.none {
    display: none;  /* Hidden */
}</code></pre>

<h3>Position Property</h3>
<pre><code>.static {
    position: static;  /* Default */
}

.relative {
    position: relative;
    top: 10px;
    left: 20px;
}

.absolute {
    position: absolute;
    top: 0;
    right: 0;
}

.fixed {
    position: fixed;
    bottom: 0;
    right: 0;
}

.sticky {
    position: sticky;
    top: 0;
}</code></pre>

<h3>Flexbox</h3>
<pre><code>.container {
    display: flex;
    justify-content: center;  /* Horizontal alignment */
    align-items: center;      /* Vertical alignment */
    gap: 10px;
}

.item {
    flex: 1;  /* Grow to fill space */
}</code></pre>

<h3>Grid Layout</h3>
<pre><code>.grid-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.grid-item {
    grid-column: span 2;
}</code></pre>

<h3>Float and Clear</h3>
<pre><code>.left {
    float: left;
    margin-right: 10px;
}

.clearfix::after {
    content: "";
    display: table;
    clear: both;
}</code></pre>';
    }

    private function getChapter5Content(): string
    {
        return '<h2>Responsive Web Design</h2>
<p>Make your websites work on all devices and screen sizes.</p>

<h3>Viewport Meta Tag</h3>
<pre><code>&lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;</code></pre>

<h3>Media Queries</h3>
<pre><code>/* Mobile first approach */
.container {
    width: 100%;
    padding: 10px;
}

/* Tablet */
@media (min-width: 768px) {
    .container {
        width: 750px;
        margin: 0 auto;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .container {
        width: 960px;
    }
}</code></pre>

<h3>Responsive Images</h3>
<pre><code>img {
    max-width: 100%;
    height: auto;
}

/* Picture element */
&lt;picture&gt;
    &lt;source media="(min-width: 768px)" srcset="large.jpg"&gt;
    &lt;source media="(min-width: 480px)" srcset="medium.jpg"&gt;
    &lt;img src="small.jpg" alt="Responsive image"&gt;
&lt;/picture&gt;</code></pre>

<h3>Responsive Typography</h3>
<pre><code>html {
    font-size: 16px;
}

h1 {
    font-size: 2rem;  /* 32px */
}

@media (min-width: 768px) {
    html {
        font-size: 18px;
    }
}</code></pre>

<h3>Flexbox for Responsive Layout</h3>
<pre><code>.flex-container {
    display: flex;
    flex-wrap: wrap;
}

.flex-item {
    flex: 1 1 300px;  /* Grow, shrink, base width */
}</code></pre>';
    }

    private function getChapter6Content(): string
    {
        return '<h2>Introduction to JavaScript</h2>
<p>JavaScript is the programming language of the web, adding interactivity and dynamic behavior.</p>

<h3>Adding JavaScript to HTML</h3>
<pre><code>&lt;!-- Inline --&gt;
&lt;button onclick="alert(\'Hello!\')"&gt;Click Me&lt;/button&gt;

&lt;!-- Internal --&gt;
&lt;script&gt;
    console.log("Hello, JavaScript!");
&lt;/script&gt;

&lt;!-- External --&gt;
&lt;script src="script.js"&gt;&lt;/script&gt;</code></pre>

<h3>Variables and Data Types</h3>
<pre><code>// Variables
let name = "Alice";
const age = 25;
var city = "Paris";  // Old way

// Data types
let number = 42;
let text = "Hello";
let isTrue = true;
let nothing = null;
let notDefined = undefined;
let array = [1, 2, 3];
let object = {name: "Bob", age: 30};</code></pre>

<h3>Operators</h3>
<pre><code>// Arithmetic
let sum = 10 + 5;
let diff = 10 - 5;
let product = 10 * 5;
let quotient = 10 / 5;

// Comparison
let isEqual = (5 == "5");   // true (loose)
let isStrictEqual = (5 === "5");  // false (strict)

// Logical
let and = true && false;  // false
let or = true || false;   // true
let not = !true;          // false</code></pre>

<h3>Functions</h3>
<pre><code>// Function declaration
function greet(name) {
    return "Hello, " + name;
}

// Arrow function
const add = (a, b) => a + b;

// Function expression
const multiply = function(a, b) {
    return a * b;
};

console.log(greet("Alice"));
console.log(add(5, 3));
console.log(multiply(4, 2));</code></pre>

<h3>Conditionals</h3>
<pre><code>let age = 18;

if (age >= 18) {
    console.log("Adult");
} else {
    console.log("Minor");
}

// Ternary operator
let status = age >= 18 ? "Adult" : "Minor";</code></pre>

<h3>Loops</h3>
<pre><code>// for loop
for (let i = 0; i < 5; i++) {
    console.log(i);
}

// while loop
let count = 0;
while (count < 5) {
    console.log(count);
    count++;
}

// for...of (arrays)
let fruits = ["apple", "banana", "orange"];
for (let fruit of fruits) {
    console.log(fruit);
}</code></pre>';
    }

    private function getChapter7Content(): string
    {
        return '<h2>JavaScript DOM Manipulation</h2>
<p>The DOM (Document Object Model) allows JavaScript to interact with HTML elements.</p>

<h3>Selecting Elements</h3>
<pre><code>// By ID
let element = document.getElementById("myId");

// By class
let elements = document.getElementsByClassName("myClass");

// By tag
let paragraphs = document.getElementsByTagName("p");

// Query selector (CSS selector)
let first = document.querySelector(".myClass");
let all = document.querySelectorAll(".myClass");</code></pre>

<h3>Modifying Content</h3>
<pre><code>// Change text
element.textContent = "New text";
element.innerHTML = "&lt;strong&gt;Bold text&lt;/strong&gt;";

// Change attributes
element.setAttribute("class", "active");
element.src = "new-image.jpg";

// Change styles
element.style.color = "red";
element.style.fontSize = "20px";</code></pre>

<h3>Creating and Adding Elements</h3>
<pre><code>// Create element
let newDiv = document.createElement("div");
newDiv.textContent = "Hello!";
newDiv.className = "box";

// Add to DOM
document.body.appendChild(newDiv);

// Insert before
let parent = document.getElementById("container");
let reference = document.getElementById("existing");
parent.insertBefore(newDiv, reference);</code></pre>

<h3>Removing Elements</h3>
<pre><code>// Remove element
element.remove();

// Remove child
parent.removeChild(child);</code></pre>

<h3>Working with Classes</h3>
<pre><code>// Add class
element.classList.add("active");

// Remove class
element.classList.remove("hidden");

// Toggle class
element.classList.toggle("visible");

// Check if has class
if (element.classList.contains("active")) {
    console.log("Element is active");
}</code></pre>

<h3>Traversing the DOM</h3>
<pre><code>// Parent
let parent = element.parentElement;

// Children
let children = element.children;
let firstChild = element.firstElementChild;
let lastChild = element.lastElementChild;

// Siblings
let next = element.nextElementSibling;
let prev = element.previousElementSibling;</code></pre>';
    }

    private function getChapter8Content(): string
    {
        return '<h2>JavaScript Events and Interactivity</h2>
<p>Events allow you to respond to user interactions like clicks, key presses, and mouse movements.</p>

<h3>Adding Event Listeners</h3>
<pre><code>// Click event
let button = document.getElementById("myButton");
button.addEventListener("click", function() {
    alert("Button clicked!");
});

// Arrow function
button.addEventListener("click", () => {
    console.log("Clicked!");
});</code></pre>

<h3>Common Events</h3>
<pre><code>// Mouse events
element.addEventListener("click", handleClick);
element.addEventListener("dblclick", handleDoubleClick);
element.addEventListener("mouseenter", handleMouseEnter);
element.addEventListener("mouseleave", handleMouseLeave);

// Keyboard events
document.addEventListener("keydown", handleKeyDown);
document.addEventListener("keyup", handleKeyUp);

// Form events
form.addEventListener("submit", handleSubmit);
input.addEventListener("input", handleInput);
input.addEventListener("change", handleChange);

// Window events
window.addEventListener("load", handleLoad);
window.addEventListener("resize", handleResize);
window.addEventListener("scroll", handleScroll);</code></pre>

<h3>Event Object</h3>
<pre><code>button.addEventListener("click", function(event) {
    console.log(event.type);      // "click"
    console.log(event.target);    // Element clicked
    console.log(event.clientX);   // Mouse X position
    console.log(event.clientY);   // Mouse Y position
    
    event.preventDefault();  // Prevent default behavior
    event.stopPropagation(); // Stop event bubbling
});</code></pre>

<h3>Form Handling</h3>
<pre><code>let form = document.getElementById("myForm");

form.addEventListener("submit", function(e) {
    e.preventDefault();  // Prevent page reload
    
    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;
    
    console.log("Name:", name);
    console.log("Email:", email);
});</code></pre>

<h3>Event Delegation</h3>
<pre><code>// Instead of adding listener to each item
let list = document.getElementById("myList");

list.addEventListener("click", function(e) {
    if (e.target.tagName === "LI") {
        console.log("Clicked:", e.target.textContent);
    }
});</code></pre>

<h3>Practical Example: Toggle Menu</h3>
<pre><code>let menuButton = document.getElementById("menuButton");
let menu = document.getElementById("menu");

menuButton.addEventListener("click", function() {
    menu.classList.toggle("open");
});</code></pre>';
    }

    private function getChapter9Content(): string
    {
        return '<h2>JavaScript Async and Fetch API</h2>
<p>Learn to work with asynchronous JavaScript and fetch data from APIs.</p>

<h3>Promises</h3>
<pre><code>// Creating a promise
let promise = new Promise((resolve, reject) => {
    let success = true;
    
    if (success) {
        resolve("Success!");
    } else {
        reject("Error!");
    }
});

// Using a promise
promise
    .then(result => console.log(result))
    .catch(error => console.error(error));</code></pre>

<h3>Async/Await</h3>
<pre><code>async function fetchData() {
    try {
        let response = await fetch("https://api.example.com/data");
        let data = await response.json();
        console.log(data);
    } catch (error) {
        console.error("Error:", error);
    }
}

fetchData();</code></pre>

<h3>Fetch API</h3>
<pre><code>// GET request
fetch("https://api.example.com/users")
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error("Error:", error));

// POST request
fetch("https://api.example.com/users", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({
        name: "Alice",
        email: "alice@example.com"
    })
})
    .then(response => response.json())
    .then(data => console.log(data));</code></pre>

<h3>Working with JSON</h3>
<pre><code>// Parse JSON string
let jsonString = \'{"name": "Bob", "age": 30}\';
let obj = JSON.parse(jsonString);
console.log(obj.name);  // Bob

// Convert to JSON string
let person = {name: "Alice", age: 25};
let json = JSON.stringify(person);
console.log(json);  // {"name":"Alice","age":25}</code></pre>

<h3>Practical Example: Fetch and Display</h3>
<pre><code>async function loadUsers() {
    try {
        let response = await fetch("https://jsonplaceholder.typicode.com/users");
        let users = await response.json();
        
        let list = document.getElementById("userList");
        
        users.forEach(user => {
            let li = document.createElement("li");
            li.textContent = user.name;
            list.appendChild(li);
        });
    } catch (error) {
        console.error("Failed to load users:", error);
    }
}

loadUsers();</code></pre>';
    }

    private function getChapter10Content(): string
    {
        return '<h2>Building a Complete Web Project</h2>
<p>Put everything together to build a real-world web application.</p>

<h3>Project Structure</h3>
<pre><code>my-project/
├── index.html
├── css/
│   └── styles.css
├── js/
│   └── script.js
└── images/
    └── logo.png</code></pre>

<h3>HTML Structure</h3>
<pre><code>&lt;!DOCTYPE html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
    &lt;title&gt;My Web App&lt;/title&gt;
    &lt;link rel="stylesheet" href="css/styles.css"&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;header&gt;
        &lt;nav&gt;
            &lt;ul&gt;
                &lt;li&gt;&lt;a href="#home"&gt;Home&lt;/a&gt;&lt;/li&gt;
                &lt;li&gt;&lt;a href="#about"&gt;About&lt;/a&gt;&lt;/li&gt;
            &lt;/ul&gt;
        &lt;/nav&gt;
    &lt;/header&gt;
    
    &lt;main&gt;
        &lt;section id="home"&gt;
            &lt;h1&gt;Welcome&lt;/h1&gt;
        &lt;/section&gt;
    &lt;/main&gt;
    
    &lt;footer&gt;
        &lt;p&gt;&copy; 2024 My Website&lt;/p&gt;
    &lt;/footer&gt;
    
    &lt;script src="js/script.js"&gt;&lt;/script&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>

<h3>CSS Styling</h3>
<pre><code>/* Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Variables */
:root {
    --primary-color: #007bff;
    --text-color: #333;
    --bg-color: #f8f9fa;
}

/* Layout */
body {
    font-family: Arial, sans-serif;
    color: var(--text-color);
    background-color: var(--bg-color);
}

header {
    background-color: var(--primary-color);
    color: white;
    padding: 1rem;
}

nav ul {
    list-style: none;
    display: flex;
    gap: 1rem;
}

nav a {
    color: white;
    text-decoration: none;
}

main {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

footer {
    text-align: center;
    padding: 1rem;
    background-color: #333;
    color: white;
}</code></pre>

<h3>JavaScript Functionality</h3>
<pre><code>// Smooth scrolling
document.querySelectorAll(\'a[href^="#"]\').forEach(anchor => {
    anchor.addEventListener("click", function(e) {
        e.preventDefault();
        let target = document.querySelector(this.getAttribute("href"));
        target.scrollIntoView({behavior: "smooth"});
    });
});

// Form validation
let form = document.getElementById("contactForm");
form.addEventListener("submit", function(e) {
    e.preventDefault();
    
    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;
    
    if (name && email) {
        console.log("Form submitted:", {name, email});
        form.reset();
    }
});</code></pre>

<h3>Best Practices</h3>
<ul>
    <li><strong>Semantic HTML</strong>: Use appropriate tags</li>
    <li><strong>Responsive Design</strong>: Mobile-first approach</li>
    <li><strong>Accessibility</strong>: Alt text, ARIA labels</li>
    <li><strong>Performance</strong>: Optimize images, minify code</li>
    <li><strong>SEO</strong>: Meta tags, structured data</li>
    <li><strong>Security</strong>: Validate inputs, sanitize data</li>
</ul>

<h3>Deployment</h3>
<p>Deploy your website using:</p>
<ul>
    <li>GitHub Pages (free)</li>
    <li>Netlify (free)</li>
    <li>Vercel (free)</li>
    <li>Traditional hosting (paid)</li>
</ul>';
    }
}
