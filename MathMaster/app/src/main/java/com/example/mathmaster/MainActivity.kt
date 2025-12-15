package com.example.mathmaster // Make sure this matches your package name

import android.graphics.Color
import android.os.Bundle
import android.widget.Button
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import kotlin.random.Random

class MainActivity : AppCompatActivity() {

    // Define UI variables
    private lateinit var tvScore: TextView
    private lateinit var tvLevel: TextView
    private lateinit var tvQuestion: TextView
    private lateinit var tvStatus: TextView
    private lateinit var btnOption1: Button
    private lateinit var btnOption2: Button
    private lateinit var btnOption3: Button
    private lateinit var btnOption4: Button

    // Game variables
    private var score = 0
    private var correctAnswer = 0
    private var currentLevel = "Easy"

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        // Initialize UI components
        tvScore = findViewById(R.id.tvScore)
        tvLevel = findViewById(R.id.tvLevel)
        tvQuestion = findViewById(R.id.tvQuestion)
        tvStatus = findViewById(R.id.tvStatus)
        btnOption1 = findViewById(R.id.btnOption1)
        btnOption2 = findViewById(R.id.btnOption2)
        btnOption3 = findViewById(R.id.btnOption3)
        btnOption4 = findViewById(R.id.btnOption4)

        // Start the game
        generateQuestion()

        // Set click listeners for buttons
        val buttons = listOf(btnOption1, btnOption2, btnOption3, btnOption4)
        for (btn in buttons) {
            btn.setOnClickListener {
                checkAnswer(btn.text.toString().toInt())
            }
        }
    }

    private fun generateQuestion() {
        // Determine difficulty based on score
        val limit: Int
        val operators: List<String>

        if (score < 10) {
            currentLevel = "Easy"
            limit = 20
            operators = listOf("+")
        } else if (score < 20) {
            currentLevel = "Medium"
            limit = 50
            operators = listOf("+", "-")
        } else {
            currentLevel = "Hard"
            limit = 100
            operators = listOf("+", "-", "*", "/")
        }

        tvLevel.text = "Level: $currentLevel"

        // Generate two random numbers
        var num1 = Random.nextInt(1, limit)
        var num2 = Random.nextInt(1, limit)
        val operator = operators.random()

        // Logic adjustments for specific operators
        if (operator == "/") {
            // For division, ensure the answer is a whole number
            // We multiply first: 5 * 3 = 15, then present "15 / 3"
            val result = num1 * num2
            correctAnswer = num1 // num1 becomes the answer
            tvQuestion.text = "$result / $num2"
        } else if (operator == "-") {
            // Avoid negative numbers for simplicity (optional)
            if (num1 < num2) {
                val temp = num1
                num1 = num2
                num2 = temp
            }
            correctAnswer = num1 - num2
            tvQuestion.text = "$num1 - $num2"
        } else if (operator == "*") {
            // Reduce limit for multiplication so numbers don't get huge
            num1 = Random.nextInt(1, 13)
            num2 = Random.nextInt(1, 13)
            correctAnswer = num1 * num2
            tvQuestion.text = "$num1 * $num2"
        } else {
            correctAnswer = num1 + num2
            tvQuestion.text = "$num1 + $num2"
        }

        setOptions(correctAnswer)
    }

    private fun setOptions(realAnswer: Int) {
        // Create a list of answers (1 correct, 3 wrong)
        val options = mutableListOf<Int>()
        options.add(realAnswer)

        while (options.size < 4) {
            // Generate wrong answers close to the real answer
            val wrongAnswer = realAnswer + Random.nextInt(-10, 10)
            if (wrongAnswer != realAnswer && wrongAnswer > 0 && !options.contains(wrongAnswer)) {
                options.add(wrongAnswer)
            }
        }

        // Shuffle the list so the correct answer isn't always first
        options.shuffle()

        // Assign to buttons
        btnOption1.text = options[0].toString()
        btnOption2.text = options[1].toString()
        btnOption3.text = options[2].toString()
        btnOption4.text = options[3].toString()
    }

    private fun checkAnswer(selectedAnswer: Int) {
        if (selectedAnswer == correctAnswer) {
            score++
            tvStatus.text = "Correct! Good job."
            tvStatus.setTextColor(Color.GREEN)
        } else {
            // Optional: Decrease score or Game Over logic
            if (score > 0) score--
            tvStatus.text = "Wrong! The answer was $correctAnswer."
            tvStatus.setTextColor(Color.RED)
        }

        tvScore.text = "Score: $score"

        // Delay slightly or generate immediately
        generateQuestion()
    }
}