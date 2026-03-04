<?php

namespace App\DataFixtures;

use App\Entity\GestionDeCours\Cours;
use App\Entity\GestionDeCours\Chapitre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JavaCourseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer le cours Java
        $cours = new Cours();
        $cours->setTitre('Java Programming for Beginners');
        $cours->setDescription('Complete Java programming course covering fundamentals to advanced concepts. Learn object-oriented programming, data structures, and build real-world applications.');
        $cours->setMatiere('Informatique');
        $cours->setNiveau('Debutant');
        $cours->setDuree(50);
        $cours->setCreatedAt(new \DateTimeImmutable());

        // Chapitre 1: Introduction to Java
        $chapitre1 = new Chapitre();
        $chapitre1->setTitre('Introduction to Java');
        $chapitre1->setOrdre(1);
        $chapitre1->setContenu($this->getChapter1Content());
        $cours->addChapitre($chapitre1);

        // Chapitre 2: Variables and Data Types
        $chapitre2 = new Chapitre();
        $chapitre2->setTitre('Variables and Data Types');
        $chapitre2->setOrdre(2);
        $chapitre2->setContenu($this->getChapter2Content());
        $cours->addChapitre($chapitre2);

        // Chapitre 3: Operators and Expressions
        $chapitre3 = new Chapitre();
        $chapitre3->setTitre('Operators and Expressions');
        $chapitre3->setOrdre(3);
        $chapitre3->setContenu($this->getChapter3Content());
        $cours->addChapitre($chapitre3);

        // Chapitre 4: Control Flow Statements
        $chapitre4 = new Chapitre();
        $chapitre4->setTitre('Control Flow Statements');
        $chapitre4->setOrdre(4);
        $chapitre4->setContenu($this->getChapter4Content());
        $cours->addChapitre($chapitre4);

        // Chapitre 5: Loops and Iterations
        $chapitre5 = new Chapitre();
        $chapitre5->setTitre('Loops and Iterations');
        $chapitre5->setOrdre(5);
        $chapitre5->setContenu($this->getChapter5Content());
        $cours->addChapitre($chapitre5);

        // Chapitre 6: Methods and Functions
        $chapitre6 = new Chapitre();
        $chapitre6->setTitre('Methods and Functions');
        $chapitre6->setOrdre(6);
        $chapitre6->setContenu($this->getChapter6Content());
        $cours->addChapitre($chapitre6);

        // Chapitre 7: Object-Oriented Programming
        $chapitre7 = new Chapitre();
        $chapitre7->setTitre('Object-Oriented Programming');
        $chapitre7->setOrdre(7);
        $chapitre7->setContenu($this->getChapter7Content());
        $cours->addChapitre($chapitre7);

        // Chapitre 8: Arrays and Collections
        $chapitre8 = new Chapitre();
        $chapitre8->setTitre('Arrays and Collections');
        $chapitre8->setOrdre(8);
        $chapitre8->setContenu($this->getChapter8Content());
        $cours->addChapitre($chapitre8);

        $manager->persist($cours);
        $manager->flush();
    }

    private function getChapter1Content(): string
    {
        return '<h2>Welcome to Java Programming</h2>
<p>Java is a powerful, versatile, and widely-used programming language. Created by James Gosling at Sun Microsystems in 1995, Java has become one of the most popular programming languages in the world.</p>

<h3>Why Learn Java?</h3>
<ul>
    <li><strong>Platform Independent</strong>: Write once, run anywhere (WORA)</li>
    <li><strong>Object-Oriented</strong>: Promotes code reusability and modularity</li>
    <li><strong>Robust and Secure</strong>: Strong memory management and security features</li>
    <li><strong>Large Ecosystem</strong>: Extensive libraries and frameworks</li>
    <li><strong>High Demand</strong>: Used by major companies worldwide</li>
</ul>

<h3>Java Applications</h3>
<p>Java is used in various domains:</p>
<ul>
    <li>Enterprise Applications (Banking, E-commerce)</li>
    <li>Android Mobile Development</li>
    <li>Web Applications (Spring, JSP)</li>
    <li>Big Data Technologies (Hadoop, Spark)</li>
    <li>Scientific Applications</li>
</ul>

<h3>Setting Up Java</h3>
<p>To start programming in Java, you need:</p>
<ol>
    <li><strong>JDK (Java Development Kit)</strong>: Download from Oracle or OpenJDK</li>
    <li><strong>IDE</strong>: IntelliJ IDEA, Eclipse, or NetBeans</li>
    <li><strong>Text Editor</strong>: VS Code, Sublime Text (optional)</li>
</ol>

<h3>Your First Java Program</h3>
<pre><code>public class HelloWorld {
    public static void main(String[] args) {
        System.out.println("Hello, World!");
        System.out.println("Welcome to Java Programming!");
    }
}</code></pre>

<p>This simple program demonstrates the basic structure of a Java application. Every Java program must have a <code>main</code> method as the entry point.</p>

<h3>Java Program Structure</h3>
<ul>
    <li><strong>Class Declaration</strong>: <code>public class HelloWorld</code></li>
    <li><strong>Main Method</strong>: <code>public static void main(String[] args)</code></li>
    <li><strong>Statements</strong>: Instructions ending with semicolon</li>
    <li><strong>Blocks</strong>: Code enclosed in curly braces <code>{}</code></li>
</ul>';
    }

    private function getChapter2Content(): string
    {
        return '<h2>Variables and Data Types in Java</h2>
<p>Variables are containers that store data values. In Java, every variable must be declared with a specific data type.</p>

<h3>Primitive Data Types</h3>
<p>Java has 8 primitive data types:</p>

<h4>Integer Types</h4>
<pre><code>byte age = 25;        // 8-bit, range: -128 to 127
short year = 2024;    // 16-bit, range: -32,768 to 32,767
int population = 1000000;  // 32-bit, most commonly used
long distance = 9876543210L;  // 64-bit, note the L suffix</code></pre>

<h4>Floating-Point Types</h4>
<pre><code>float price = 19.99f;  // 32-bit, note the f suffix
double pi = 3.14159265359;  // 64-bit, more precise</code></pre>

<h4>Character and Boolean</h4>
<pre><code>char grade = \'A\';  // Single character, 16-bit Unicode
boolean isStudent = true;  // true or false</code></pre>

<h3>Reference Types</h3>
<pre><code>String name = "Alice";  // String is a reference type
String message = "Hello, Java!";</code></pre>

<h3>Variable Declaration and Initialization</h3>
<pre><code>// Declaration
int number;

// Initialization
number = 42;

// Declaration and initialization together
int score = 100;

// Multiple variables
int x = 10, y = 20, z = 30;</code></pre>

<h3>Type Casting</h3>
<pre><code>// Implicit casting (widening)
int num = 100;
double decimal = num;  // int to double

// Explicit casting (narrowing)
double pi = 3.14;
int intPi = (int) pi;  // 3 (decimal part lost)

// String to number
String strNum = "123";
int converted = Integer.parseInt(strNum);</code></pre>

<h3>Constants</h3>
<pre><code>final double PI = 3.14159;
final int MAX_USERS = 100;
// Constants cannot be changed after initialization</code></pre>';
    }

    private function getChapter3Content(): string
    {
        return '<h2>Operators and Expressions</h2>
<p>Operators are symbols that perform operations on variables and values.</p>

<h3>Arithmetic Operators</h3>
<pre><code>int a = 10, b = 3;

int sum = a + b;        // 13 (Addition)
int diff = a - b;       // 7  (Subtraction)
int product = a * b;    // 30 (Multiplication)
int quotient = a / b;   // 3  (Division)
int remainder = a % b;  // 1  (Modulus)

// Increment and Decrement
int x = 5;
x++;  // x = 6 (post-increment)
++x;  // x = 7 (pre-increment)
x--;  // x = 6 (post-decrement)
--x;  // x = 5 (pre-decrement)</code></pre>

<h3>Comparison Operators</h3>
<pre><code>int x = 10, y = 20;

boolean isEqual = (x == y);      // false
boolean notEqual = (x != y);     // true
boolean greater = (x > y);       // false
boolean less = (x < y);          // true
boolean greaterOrEqual = (x >= y);  // false
boolean lessOrEqual = (x <= y);     // true</code></pre>

<h3>Logical Operators</h3>
<pre><code>boolean a = true, b = false;

boolean and = a && b;  // false (AND)
boolean or = a || b;   // true  (OR)
boolean not = !a;      // false (NOT)

// Short-circuit evaluation
boolean result = (x > 5) && (y < 30);  // Both conditions checked
boolean result2 = (x < 5) && (y < 30); // Second not checked if first is false</code></pre>

<h3>Assignment Operators</h3>
<pre><code>int x = 10;

x += 5;  // x = x + 5  (x = 15)
x -= 3;  // x = x - 3  (x = 12)
x *= 2;  // x = x * 2  (x = 24)
x /= 4;  // x = x / 4  (x = 6)
x %= 4;  // x = x % 4  (x = 2)</code></pre>

<h3>Ternary Operator</h3>
<pre><code>int age = 18;
String status = (age >= 18) ? "Adult" : "Minor";
System.out.println(status);  // Adult

// Nested ternary
int score = 85;
String grade = (score >= 90) ? "A" : (score >= 80) ? "B" : "C";</code></pre>';
    }

    private function getChapter4Content(): string
    {
        return '<h2>Control Flow Statements</h2>
<p>Control flow statements allow your program to make decisions and execute different code paths based on conditions.</p>

<h3>if Statement</h3>
<pre><code>int age = 18;

if (age >= 18) {
    System.out.println("You are an adult");
    System.out.println("You can vote");
}</code></pre>

<h3>if-else Statement</h3>
<pre><code>int temperature = 25;

if (temperature > 30) {
    System.out.println("It\'s hot outside");
} else {
    System.out.println("The weather is pleasant");
}</code></pre>

<h3>if-else-if Ladder</h3>
<pre><code>int score = 85;

if (score >= 90) {
    System.out.println("Grade: A");
} else if (score >= 80) {
    System.out.println("Grade: B");
} else if (score >= 70) {
    System.out.println("Grade: C");
} else if (score >= 60) {
    System.out.println("Grade: D");
} else {
    System.out.println("Grade: F");
}</code></pre>

<h3>Nested if Statements</h3>
<pre><code>int age = 20;
boolean hasLicense = true;

if (age >= 18) {
    if (hasLicense) {
        System.out.println("You can drive");
    } else {
        System.out.println("You need a license");
    }
} else {
    System.out.println("You are too young to drive");
}</code></pre>

<h3>switch Statement</h3>
<pre><code>int day = 3;
String dayName;

switch (day) {
    case 1:
        dayName = "Monday";
        break;
    case 2:
        dayName = "Tuesday";
        break;
    case 3:
        dayName = "Wednesday";
        break;
    case 4:
        dayName = "Thursday";
        break;
    case 5:
        dayName = "Friday";
        break;
    case 6:
        dayName = "Saturday";
        break;
    case 7:
        dayName = "Sunday";
        break;
    default:
        dayName = "Invalid day";
}

System.out.println(dayName);  // Wednesday</code></pre>

<h3>switch with Strings</h3>
<pre><code>String month = "January";

switch (month) {
    case "December":
    case "January":
    case "February":
        System.out.println("Winter");
        break;
    case "March":
    case "April":
    case "May":
        System.out.println("Spring");
        break;
    case "June":
    case "July":
    case "August":
        System.out.println("Summer");
        break;
    default:
        System.out.println("Fall");
}</code></pre>';
    }

    private function getChapter5Content(): string
    {
        return '<h2>Loops and Iterations</h2>
<p>Loops allow you to execute a block of code repeatedly.</p>

<h3>for Loop</h3>
<pre><code>// Basic for loop
for (int i = 0; i < 5; i++) {
    System.out.println("Iteration: " + i);
}
// Output: 0, 1, 2, 3, 4

// Loop with different step
for (int i = 0; i <= 10; i += 2) {
    System.out.println(i);  // 0, 2, 4, 6, 8, 10
}

// Countdown
for (int i = 10; i >= 1; i--) {
    System.out.println(i);
}
System.out.println("Liftoff!");</code></pre>

<h3>while Loop</h3>
<pre><code>int count = 0;

while (count < 5) {
    System.out.println("Count: " + count);
    count++;
}

// User input validation
Scanner scanner = new Scanner(System.in);
String answer = "";

while (!answer.equals("yes")) {
    System.out.print("Do you want to continue? (yes/no): ");
    answer = scanner.nextLine();
}</code></pre>

<h3>do-while Loop</h3>
<pre><code>int number;

do {
    System.out.print("Enter a positive number: ");
    number = scanner.nextInt();
} while (number <= 0);

System.out.println("You entered: " + number);</code></pre>

<h3>Enhanced for Loop (for-each)</h3>
<pre><code>int[] numbers = {1, 2, 3, 4, 5};

for (int num : numbers) {
    System.out.println(num);
}

String[] fruits = {"Apple", "Banana", "Orange"};

for (String fruit : fruits) {
    System.out.println("I like " + fruit);
}</code></pre>

<h3>Loop Control Statements</h3>
<pre><code>// break - exit the loop
for (int i = 0; i < 10; i++) {
    if (i == 5) {
        break;  // Stop when i is 5
    }
    System.out.println(i);  // 0, 1, 2, 3, 4
}

// continue - skip current iteration
for (int i = 0; i < 10; i++) {
    if (i % 2 == 0) {
        continue;  // Skip even numbers
    }
    System.out.println(i);  // 1, 3, 5, 7, 9
}</code></pre>

<h3>Nested Loops</h3>
<pre><code>// Multiplication table
for (int i = 1; i <= 5; i++) {
    for (int j = 1; j <= 5; j++) {
        System.out.print(i * j + "\t");
    }
    System.out.println();
}

// Pattern printing
for (int i = 1; i <= 5; i++) {
    for (int j = 1; j <= i; j++) {
        System.out.print("* ");
    }
    System.out.println();
}</code></pre>';
    }

    private function getChapter6Content(): string
    {
        return '<h2>Methods and Functions</h2>
<p>Methods are reusable blocks of code that perform specific tasks.</p>

<h3>Method Declaration</h3>
<pre><code>public class Calculator {
    // Method without parameters
    public static void greet() {
        System.out.println("Hello!");
        System.out.println("Welcome to Java");
    }
    
    // Call the method
    public static void main(String[] args) {
        greet();
    }
}</code></pre>

<h3>Methods with Parameters</h3>
<pre><code>public static void greetPerson(String name) {
    System.out.println("Hello, " + name + "!");
}

public static void main(String[] args) {
    greetPerson("Alice");
    greetPerson("Bob");
}

// Multiple parameters
public static void displayInfo(String name, int age) {
    System.out.println("Name: " + name);
    System.out.println("Age: " + age);
}

displayInfo("Charlie", 25);</code></pre>

<h3>Return Values</h3>
<pre><code>public static int add(int a, int b) {
    return a + b;
}

public static double calculateArea(double radius) {
    return Math.PI * radius * radius;
}

public static void main(String[] args) {
    int sum = add(5, 3);
    System.out.println("Sum: " + sum);  // 8
    
    double area = calculateArea(5.0);
    System.out.println("Area: " + area);
}</code></pre>

<h3>Method Overloading</h3>
<pre><code>public class MathOperations {
    // Same method name, different parameters
    public static int multiply(int a, int b) {
        return a * b;
    }
    
    public static double multiply(double a, double b) {
        return a * b;
    }
    
    public static int multiply(int a, int b, int c) {
        return a * b * c;
    }
    
    public static void main(String[] args) {
        System.out.println(multiply(5, 3));        // 15
        System.out.println(multiply(2.5, 4.0));    // 10.0
        System.out.println(multiply(2, 3, 4));     // 24
    }
}</code></pre>

<h3>Variable Arguments (Varargs)</h3>
<pre><code>public static int sum(int... numbers) {
    int total = 0;
    for (int num : numbers) {
        total += num;
    }
    return total;
}

public static void main(String[] args) {
    System.out.println(sum(1, 2, 3));           // 6
    System.out.println(sum(10, 20, 30, 40));    // 100
    System.out.println(sum(5));                 // 5
}</code></pre>

<h3>Recursion</h3>
<pre><code>public static int factorial(int n) {
    if (n == 0 || n == 1) {
        return 1;  // Base case
    }
    return n * factorial(n - 1);  // Recursive call
}

public static int fibonacci(int n) {
    if (n <= 1) {
        return n;
    }
    return fibonacci(n - 1) + fibonacci(n - 2);
}

public static void main(String[] args) {
    System.out.println("5! = " + factorial(5));  // 120
    System.out.println("Fib(7) = " + fibonacci(7));  // 13
}</code></pre>';
    }

    private function getChapter7Content(): string
    {
        return '<h2>Object-Oriented Programming</h2>
<p>OOP is a programming paradigm based on the concept of objects that contain data and code.</p>

<h3>Classes and Objects</h3>
<pre><code>public class Person {
    // Instance variables (attributes)
    String name;
    int age;
    
    // Constructor
    public Person(String name, int age) {
        this.name = name;
        this.age = age;
    }
    
    // Method
    public void introduce() {
        System.out.println("Hi, I\'m " + name + " and I\'m " + age + " years old");
    }
}

// Creating objects
public class Main {
    public static void main(String[] args) {
        Person person1 = new Person("Alice", 25);
        Person person2 = new Person("Bob", 30);
        
        person1.introduce();
        person2.introduce();
    }
}</code></pre>

<h3>Encapsulation</h3>
<pre><code>public class BankAccount {
    private double balance;  // Private variable
    
    public BankAccount(double initialBalance) {
        this.balance = initialBalance;
    }
    
    // Getter
    public double getBalance() {
        return balance;
    }
    
    // Methods to modify balance
    public void deposit(double amount) {
        if (amount > 0) {
            balance += amount;
            System.out.println("Deposited: $" + amount);
        }
    }
    
    public void withdraw(double amount) {
        if (amount > 0 && amount <= balance) {
            balance -= amount;
            System.out.println("Withdrawn: $" + amount);
        } else {
            System.out.println("Insufficient funds");
        }
    }
}</code></pre>

<h3>Inheritance</h3>
<pre><code>// Parent class
public class Animal {
    String name;
    
    public Animal(String name) {
        this.name = name;
    }
    
    public void makeSound() {
        System.out.println("Some generic sound");
    }
}

// Child classes
public class Dog extends Animal {
    public Dog(String name) {
        super(name);  // Call parent constructor
    }
    
    @Override
    public void makeSound() {
        System.out.println(name + " says: Woof!");
    }
}

public class Cat extends Animal {
    public Cat(String name) {
        super(name);
    }
    
    @Override
    public void makeSound() {
        System.out.println(name + " says: Meow!");
    }
}

// Usage
Dog dog = new Dog("Rex");
Cat cat = new Cat("Whiskers");
dog.makeSound();  // Rex says: Woof!
cat.makeSound();  // Whiskers says: Meow!</code></pre>

<h3>Polymorphism</h3>
<pre><code>public class Main {
    public static void main(String[] args) {
        Animal[] animals = new Animal[3];
        animals[0] = new Dog("Buddy");
        animals[1] = new Cat("Mittens");
        animals[2] = new Animal("Generic");
        
        for (Animal animal : animals) {
            animal.makeSound();  // Different behavior for each type
        }
    }
}</code></pre>

<h3>Abstract Classes</h3>
<pre><code>public abstract class Shape {
    abstract double calculateArea();
    
    public void display() {
        System.out.println("Area: " + calculateArea());
    }
}

public class Circle extends Shape {
    double radius;
    
    public Circle(double radius) {
        this.radius = radius;
    }
    
    @Override
    double calculateArea() {
        return Math.PI * radius * radius;
    }
}

public class Rectangle extends Shape {
    double width, height;
    
    public Rectangle(double width, double height) {
        this.width = width;
        this.height = height;
    }
    
    @Override
    double calculateArea() {
        return width * height;
    }
}</code></pre>';
    }

    private function getChapter8Content(): string
    {
        return '<h2>Arrays and Collections</h2>
<p>Arrays and collections are used to store multiple values in a single variable.</p>

<h3>Arrays</h3>
<pre><code>// Array declaration and initialization
int[] numbers = {1, 2, 3, 4, 5};
String[] fruits = {"Apple", "Banana", "Orange"};

// Array with size
int[] scores = new int[5];
scores[0] = 90;
scores[1] = 85;
scores[2] = 92;

// Accessing elements
System.out.println(numbers[0]);  // 1
System.out.println(fruits[2]);   // Orange

// Array length
System.out.println("Length: " + numbers.length);  // 5</code></pre>

<h3>Iterating Arrays</h3>
<pre><code>// Using for loop
for (int i = 0; i < numbers.length; i++) {
    System.out.println(numbers[i]);
}

// Using enhanced for loop
for (int num : numbers) {
    System.out.println(num);
}

// Using Arrays class
import java.util.Arrays;

int[] arr = {5, 2, 8, 1, 9};
Arrays.sort(arr);  // Sort array
System.out.println(Arrays.toString(arr));  // [1, 2, 5, 8, 9]</code></pre>

<h3>Multidimensional Arrays</h3>
<pre><code>// 2D array
int[][] matrix = {
    {1, 2, 3},
    {4, 5, 6},
    {7, 8, 9}
};

// Accessing elements
System.out.println(matrix[1][2]);  // 6

// Iterating 2D array
for (int i = 0; i < matrix.length; i++) {
    for (int j = 0; j < matrix[i].length; j++) {
        System.out.print(matrix[i][j] + " ");
    }
    System.out.println();
}</code></pre>

<h3>ArrayList</h3>
<pre><code>import java.util.ArrayList;

// Creating ArrayList
ArrayList<String> names = new ArrayList<>();

// Adding elements
names.add("Alice");
names.add("Bob");
names.add("Charlie");

// Accessing elements
System.out.println(names.get(0));  // Alice

// Size
System.out.println("Size: " + names.size());  // 3

// Removing elements
names.remove("Bob");
names.remove(0);  // Remove by index

// Iterating
for (String name : names) {
    System.out.println(name);
}</code></pre>

<h3>HashMap</h3>
<pre><code>import java.util.HashMap;

// Creating HashMap
HashMap<String, Integer> ages = new HashMap<>();

// Adding key-value pairs
ages.put("Alice", 25);
ages.put("Bob", 30);
ages.put("Charlie", 28);

// Getting values
System.out.println(ages.get("Alice"));  // 25

// Checking if key exists
if (ages.containsKey("Bob")) {
    System.out.println("Bob\'s age: " + ages.get("Bob"));
}

// Iterating
for (String name : ages.keySet()) {
    System.out.println(name + ": " + ages.get(name));
}</code></pre>

<h3>HashSet</h3>
<pre><code>import java.util.HashSet;

// Creating HashSet (no duplicates)
HashSet<Integer> numbers = new HashSet<>();

numbers.add(1);
numbers.add(2);
numbers.add(3);
numbers.add(2);  // Duplicate, won\'t be added

System.out.println(numbers);  // [1, 2, 3]

// Checking if element exists
if (numbers.contains(2)) {
    System.out.println("2 is in the set");
}

// Removing element
numbers.remove(1);

// Size
System.out.println("Size: " + numbers.size());</code></pre>

<h3>Collections Utility Methods</h3>
<pre><code>import java.util.Collections;
import java.util.ArrayList;

ArrayList<Integer> list = new ArrayList<>();
list.add(5);
list.add(2);
list.add(8);
list.add(1);

// Sort
Collections.sort(list);
System.out.println(list);  // [1, 2, 5, 8]

// Reverse
Collections.reverse(list);
System.out.println(list);  // [8, 5, 2, 1]

// Max and Min
System.out.println("Max: " + Collections.max(list));  // 8
System.out.println("Min: " + Collections.min(list));  // 1

// Shuffle
Collections.shuffle(list);
System.out.println(list);  // Random order</code></pre>';
    }
}